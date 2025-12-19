<?php

use app\models\Author;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?= Html::a('Топ авторов', ['top'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php
    $columns = [
            [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => '№',
            ],
            'id',
            'last_name',
            'first_name',
            'middle_name',
            [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'headerOptions' => ['style' => 'width: 150px;'],
            ],
            [
                    'header' => 'Подписка',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a(
                                '<span class="glyphicon glyphicon-envelope"></span> Подписаться',
                                ['subscribe', 'id' => $model->id],
                                [
                                        'class' => 'btn btn-sm btn-info',
                                        'title' => 'Получать уведомления о новых книгах',
                                        'data-pjax' => '0',
                                ]
                        );
                    },
                    'headerOptions' => ['style' => 'width: 120px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
            ],
    ];

    if (!Yii::$app->user->isGuest) {
        $columns[] = [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Author $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'headerOptions' => ['style' => 'width: 100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
        ];
    }
    ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'summary' => 'Показано {begin}-{end} из {totalCount}',
            'tableOptions' => ['class' => 'table table-striped table-bordered'],
    ]); ?>
</div>