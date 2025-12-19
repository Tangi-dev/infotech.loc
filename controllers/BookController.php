<?php

namespace app\controllers;

use app\components\SmsService;
use app\models\Book;
use app\models\BookAuthor;
use app\models\bookSearch;
use app\models\Subscription;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete', 'delete-image'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-image' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'yearsList' => BookSearch::getYearsList(),
            'authorsList' => BookSearch::getAuthorsList(),
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                if ($model->validate()) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($model->save(false)) {
                            if ($model->imageFile && $model->upload()) {
                                $model->save(false);
                            }
                            $model->saveAuthors();
                            $smsService = new SmsService();
                            $smsService->sendNotifications($model);
                            $transaction->commit();

                            return $this->redirect(['view', 'id' => $model->id]);
                        } else {
                            $transaction->rollBack();
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::error('Ошибка при создании книги: ' . $e->getMessage());
                        Yii::$app->session->Flash('error', 'Ошибка при создании книги');
                    }
                } else {
                    Yii::error('Ошибки валидации: ' . json_encode($model->errors));
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                if ($model->validate()) {
                    if (!$model->imageFile || !$model->upload()) {
                        $model->image = $oldImage;
                    }

                    if ($model->save(false)) {
                        $model->saveAuthors();
                        Yii::$app->session->setFlash('success', 'Книга успешно обновлена');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return Book the loaded model
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Удаляем обложку
     *
     * @param int $id
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function actionDeleteImage($id)
    {
        $model = $this->findModel($id);

        if ($model->image) {
            $imagePath = $model->getUploadPath() . $model->image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $model->image = null;
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Обложка удалена');
        }

        return $this->redirect(['update', 'id' => $model->id]);
    }
}
