<?php

namespace app\controllers;
use app\models\Author;
use app\models\Subscription;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AuthorController implements the CRUD actions for Author model.
 */
class AuthorController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'top', 'subscribe'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Author models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Author::find(),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Author model.
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
     * Creates a new Author model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Author();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Author model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Author model.
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
     * Finds the Author model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return Author the loaded model
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Топ авторов
     * @throws Exception
     *
     * @return string
     */
    public function actionTop()
    {
        $startYear = (int)date('Y');
        $endYear = 1900;
        $allYearsData = Author::getTopAuthorsByYearRange($startYear, $endYear, 10);

        return $this->render('top', [
            'allYearsData' => $allYearsData,
        ]);
    }

    /**
     * Подписка на автора
     *
     * @param int $id ID автора
     * @return string|Response
     */
    public function actionSubscribe($id)
    {
        $author = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $phone = Yii::$app->request->post('Subscription')['phone'];
            $result = Subscription::createSubscription($phone, $id, $author);

            if ($result['success']) {
                Yii::$app->session->setFlash('success', 'Подписка успешно выполнена');
                return $this->redirect(['view', 'id' => $id]);
            } else {
                $flashType = isset($result['alreadyExists']) ? 'warning' : 'error';
                Yii::$app->session->setFlash($flashType, $result['message']);

                if (isset($result['alreadyExists'])) {
                    return $this->redirect(['view', 'id' => $id]);
                }
            }
        }

        $model = new Subscription();
        $model->author_id = $id;

        return $this->render('subscribe', [
            'model' => $model,
            'author' => $author,
        ]);
    }
}
