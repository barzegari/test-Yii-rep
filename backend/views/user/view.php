<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\User;
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
			'avatar:image:Avatar',
            'created_at:datetime:Member since',
            ['label'=> 'Status', 'value'=> ($model->status == 10) ? 'Active':'Deleted'],
        ],
    ];
    echo DetailView::widget($details) ?>
    <p>
        <?php if (!Yii::$app->user->isGuest ){ ?>
        <?= Html::a('Edit User', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete User', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii','Are you sure you want to delete your profile?'),
                'method' => 'post',
            ],
        ]) ;
		//Yii::$app->user->findIdentity($model->id)->avatar;
		echo Html::a( Yii::t('yii', 'Users List'), ['index'], ['class' => 'btn btn-success']); 
		} 
		?>
    </p>
</div>