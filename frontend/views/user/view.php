<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model common\models\User */
$this->title = $model->username;
$avatar_url = $model->getImageUrl() ;
?>
<div class="user-view">
    <?php
    echo Html::img($avatar_url, ['class'=>'img-responsive center-block']);
    ?>
    <h1><?= Html::encode($this->title) ?></h1>



    <?php
    $details = [
        'model' => $model,
        'attributes' => [
            'username:text:Username',
            'email:email:Email',
            'mobile:text:Mobile Number',
            'created_at:datetime:Member since',
            ['label'=> 'Status', 'value'=> ($model->status == 10) ? 'Active':'Deleted'],
        ],
    ];
    echo DetailView::widget($details) ?>
    <p>
        <?php if (!Yii::$app->user->isGuest && $model->id === Yii::$app->user->identity->id){ ?>
        <?= Html::a('Edit Profile', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete Profile', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete your profile?',
                'method' => 'post',
            ],
        ]) ?>
        <?php } ?>
    </p>
</div>