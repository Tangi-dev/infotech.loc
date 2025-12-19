<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                    ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'year',
            'description:ntext',
            'isbn',
            'image',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <?php if ($model->image): ?>
        <div class="form-group">
            <?= Html::img($model->getImageUrl(), ['class' => 'img-thumbnail', 'style' => 'max-width: 300px;']) ?>
        </div>
    <?php endif; ?>

</div>
