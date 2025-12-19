<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Author;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number', 'min' => 1900, 'max' => date('Y')]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'authorIds')->dropDownList(
            ArrayHelper::map(Author::find()->orderBy('last_name, first_name')->all(), 'id', 'fullName'),
            [
                    'multiple' => true,
                    'class' => 'form-control',
                    'size' => 10,
            ]
    )->label('Авторы') ?>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <?php if ($model->image): ?>
        <div class="form-group">
            <?= Html::a('Удалить обложку', ['delete-image', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-xs',
                    'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить обложку?',
                            'method' => 'post',
                    ],
            ]) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>