<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\web\User;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="form-group col-lg-12">
    	<div class="col-lg-12 pull-left">
	<?php
    $form = ActiveForm::begin([
		'options'=>['enctype'=>'multipart/form-data'] // important
	]);
    if ($model->isNewRecord){
        echo $form->field($model, 'username')->input('text');
    }
    ?>  </div>
    	<div class="col-lg-6"> 
    <?= $form->field($model, 'email')->input('email') ?>
   		</div>
    	<div class="col-lg-6"> 
    <?= $form->field($model, 'mobile')->input([])->label('Mobile') ?>
   		</div>
    	<div class="col-lg-6"> 
    <?= $form->field($model, 'new_password')->passwordInput(['value'=>'']) ?>
   		</div>
    	<div class="col-lg-6"> 
    <?= $form->field($model, 'repeat_password')->passwordInput(['value'=>'']) ?>
	    </div>
    	<div class="col-lg-6"> 
    <?= $form->field($model, 'avatar')->fileInput()?>
   		</div>
        <div class="col-lg-6"> 
	<?php
		
		// display the image uploaded or show a placeholder
		$title = 'Avatar';
		echo Html::img('/uploads/'.$model->avatar, [
			'class'=>'', 
			'alt'=>$title, 
			'title'=>$title
		]);
	?>
    	</div>
		<div class="form-group col-lg-12">
		<?php
		
		echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success ' : 'btn btn-primary ']); 
		
        if(!$model->isNewRecord) { 
            echo Html::a('Delete', ['/user/delete', 'id'=>$model->id], ['class'=>'btn btn-danger']);
        }

		echo Html::a( Yii::t('yii', 'Users List'), ['index'], ['class' => 'btn btn-success']); 
		
		ActiveForm::end(); ?>
	</div>
</div>