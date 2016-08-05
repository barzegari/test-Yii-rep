<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\User */
$this->title = Yii::t('yii', 'Add User');
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('user_form_template', [
        'model' => $model,
    ]) ?>

</div>