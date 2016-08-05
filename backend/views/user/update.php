<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model common\models\User */
$this->title =Yii::t('yii', 'Edit  User ' . ' ' . $model->username) ;
$avatar_url = $model->getImageUrl() ;
?>
<div class="row ">
    
	<?= Html::img($avatar_url, ['class'=>'img-responsive center-block'])?>
    
    <h2 class="alert-waring"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('user_form_template', [
        'model' => $model,
    ]) ?>

</div>