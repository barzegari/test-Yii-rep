<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="form-group col-lg-5">
        <?= $form->field($model, 'email')->input('email') ?>
        <?= $form->field($model, 'mobile')->input([])->label('Mobile Number') ?>
        <?= $form->field($model, 'old_password')->passwordInput(['value' => '']) ?>
        <?= $form->field($model, 'new_password')->passwordInput(['value' => '']) ?>
        <?= $form->field($model, 'repeat_password')->passwordInput(['value' => '']) ?>
        <?= $form->field($model, 'avatar')->fileInput()->label('Choose a new avatar') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>