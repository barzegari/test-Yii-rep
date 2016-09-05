<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\User;
/* @var $this yii\web\View */
/* @var $model common\models\User */
$this->title = $model->username;
?>
<div class="user-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
	
    $details = [
        'model' => $model,
        'attributes' => [
            'username:text:Username',
            'email:email:Email',
            'mobile:text:Mobile Number',
			[
			  'attribute'=>'image',
			  'label'=> 'Avatar Picture',
			  'value'=> '/uploads/' . $model->avatar,
			  'format'=>['image',['width'=>100, 'height'=>100]]
    		],
            'created_at:datetime:Member since',
            ['label'=> 'Status', 'value'=> ($model->status == 10) ? 'Active':'Deleted'],
        ],
    ];
    echo DetailView::widget($details) ;
	?>
    <p>
        <?php if (!Yii::$app->user->isGuest ){ ?>
        <?= Html::a('Edit User', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete User', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii','Are you sure you want to delete your profile?'),
                'method' => 'post',
            ],
        ]) ;?>

		<?= Html::a( Yii::t('yii', 'Users List'), ['index'], ['class' => 'btn btn-success']) ?>
		 
		<?php 
			} 
		?>
    </p>
</div>