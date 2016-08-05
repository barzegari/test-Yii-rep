<?php
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Members Index';
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            'email:email',
            'mobile:text:Mobile Number',
            // 'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn','template' => '{view}',],
        ],
    ]); ?>

</div>