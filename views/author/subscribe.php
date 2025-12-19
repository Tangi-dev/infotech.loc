<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Subscription $model */
/** @var app\models\Author $author */

$this->title = 'Подписаться на автора: ' . $author->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $author->fullName, 'url' => ['view', 'id' => $author->id]];
$this->params['breadcrumbs'][] = 'Подписаться';
?>
    <div class="author-subscribe">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="alert alert-info">
            <p>Укажите ваш номер телефона, и мы отправим SMS, когда у автора <strong><?= Html::encode($author->fullName) ?></strong> выйдет новая книга.</p>
            <p>Формат телефона: <strong>+7XXXXXXXXXX</strong> (11 цифр, начиная с +7)</p>
            <p class="small text-muted">Для тестирования используется эмулятор SMS, реальные сообщения не отправляются.</p>
        </div>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'phone')->textInput([
            'placeholder' => '+79991234567',
            'maxlength' => true,
            'class' => 'form-control',
        ])->label('Ваш номер телефона') ?>

        <?= $form->field($model, 'author_id')->hiddenInput()->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена', ['view', 'id' => $author->id], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$this->registerJs(<<<'JS'
$(document).ready(function() {
    $('#subscription-phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            if (!value.startsWith('7')) {
                value = '7' + value;
            }
            $(this).val('+' + value.substring(0, 11));
        }
    });
});
JS
);
?>