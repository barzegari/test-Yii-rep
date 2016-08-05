<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\User */
$this->title = 'Edit  ' . ' ' . $model->username . "'s profile " ;
$avatar_url = $model->getImageUrl();
?>
<div class="user-update">
    <?php
    echo Html::img($avatar_url, ['class'=>'img-responsive center-block']);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('user_form_template', [
        'model' => $model,
    ]) ?>

</div>