<?php

use app\models\Book;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Книжный каталог';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => 'Показано {begin}-{end} из {totalCount} книг',
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'title',
                    'year',
                    [
                            'attribute' => 'description',
                            'format' => 'ntext',
                            'value' => function($model) {
                                return mb_strlen($model->description) > 100
                                        ? mb_substr($model->description, 0, 100) . '...'
                                        : $model->description;
                            },
                            'headerOptions' => ['style' => 'width: 300px;'],
                    ],
                    'isbn',
                    [
                            'attribute' => 'image',
                            'label' => 'Обложка',
                            'format' => 'raw',
                            'value' => function($model) {
                                if ($model->image && $model->getImageUrl()) {
                                    return Html::img($model->getImageUrl(), [
                                            'style' => 'max-width: 80px; max-height: 80px;',
                                            'class' => 'img-thumbnail',
                                            'alt' => $model->title,
                                            'title' => $model->title,
                                    ]);
                                } else {
                                    return Html::tag('span', '-', ['class' => 'text-muted']);
                                }
                            },
                            'headerOptions' => ['style' => 'width: 100px;'],
                            'contentOptions' => ['style' => 'text-align: center;'],
                    ],
                    [
                            'attribute' => 'created_at',
                            'format' => 'datetime',
                            'headerOptions' => ['style' => 'width: 150px;'],
                    ],
                    [
                            'attribute' => 'updated_at',
                            'format' => 'datetime',
                            'headerOptions' => ['style' => 'width: 150px;'],
                    ],
                    [
                            'class' => ActionColumn::class,
                            'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            },
                            'headerOptions' => ['style' => 'width: 100px;'],
                            'contentOptions' => ['style' => 'text-align: center;'],
                    ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered'],
    ]); ?>

</div>