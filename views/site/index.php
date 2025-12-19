<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\bookSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\bookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $yearsList array */
/* @var $authorsList array */

$this->title = 'Книжный каталог';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Книжный каталог</h1>
        <?= Html::a('Перейти к авторам', ['/author/index'], ['class' => 'btn btn-lg btn-primary']) ?>
        <?= Html::a('Топ авторов', ['/author/top'], ['class' => 'btn btn-lg btn-info']) ?>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <?php Pjax::begin(['timeout' => 5000]); ?>

                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'summary' => '{end} из {totalCount}',
                        'layout' => "{summary}\n{items}\n{pager}",
                        'options' => ['class' => 'table-responsive'],
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'columns' => [
                                [
                                        'class' => 'yii\grid\SerialColumn',
                                        'header' => '№',
                                ],
                                [
                                        'attribute' => 'title',
                                        'label' => 'Название',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            return Html::a(Html::encode($model->title), ['book/view', 'id' => $model->id], [
                                                    'title' => 'Подробная информация',
                                                    'data-pjax' => '0',
                                            ]);
                                        },
                                ],
                                [
                                        'attribute' => 'year',
                                        'label' => 'Год издания',
                                        'filter' => isset($yearsList) ? $yearsList : bookSearch::getYearsList(),
                                        'filterInputOptions' => [
                                                'prompt' => 'Год',
                                                'class' => 'form-control',
                                        ],
                                ],
                                [
                                        'attribute' => 'isbn',
                                        'label' => 'ISBN',
                                        'filterInputOptions' => [
                                                'placeholder' => 'Введите ISBN',
                                                'class' => 'form-control',
                                        ],
                                ],
                                [
                                        'attribute' => 'authorIds',
                                        'label' => 'Авторы',
                                        'value' => function($model) {
                                            return $model->authorsString;
                                        },
                                        'filter' => isset($authorsList) ? $authorsList : bookSearch::getAuthorsList(),
                                        'filterInputOptions' => [
                                                'prompt' => 'Все авторы',
                                                'class' => 'form-control',
                                        ],
                                ],
                                [
                                        'attribute' => 'image',
                                        'label' => 'Обложка',
                                        'format' => 'html',
                                        'value' => function($model) {
                                            $imageUrl = $model->getImageUrl();
                                            if ($imageUrl) {
                                                return Html::img($imageUrl, [
                                                        'width' => '90',
                                                        'height' => '90',
                                                        'style' => 'object-fit: cover; border-radius: 4px;',
                                                        'alt' => $model->title,
                                                        'class' => 'book-cover',
                                                ]);
                                            }

                                            return '-';
                                        },
                                        'filter' => false,
                                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
                                        'headerOptions' => ['style' => 'text-align: center;'],
                                ],
                                [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Действия',
                                        'template' => '{view}',
                                        'buttons' => [
                                                'view' => function ($url, $model) {
                                                    return Html::a(
                                                            '<span class="glyphicon glyphicon-eye-open"></span>',
                                                            $url,
                                                            [
                                                                    'title' => 'Посмотреть',
                                                                    'class' => 'btn btn-xs btn-default',
                                                                    'data-pjax' => '0',
                                                            ]
                                                    );
                                                },
                                        ],
                                        'visible' => true,
                                ],
                        ],
                        'pager' => [
                                'options' => ['class' => 'pagination'],
                                'prevPageLabel' => '←',
                                'nextPageLabel' => '→',
                                'maxButtonCount' => 5,
                        ],
                ]); ?>

                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>