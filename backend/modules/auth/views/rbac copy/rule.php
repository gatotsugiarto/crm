<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rule Permissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
	<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">

    <p>
        <?php if ($dataProvider->totalCount < 1) { ?>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Create Rule', ['createrule'], ['class' => 'btn btn-primary', 'style' => 'width:150px']) ?>
        <?php } ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'description:ntext',
            //'rule_name',
            //'data:ntext',
            //'created_at',
            // 'updated_at',

            [
            	'class' => 'yii\grid\ActionColumn', 
            	'template' => '{manage}',
            			'buttons'=>[
                  	//'view' => function ($url, $model, $key) {     
                  	//  		$url = \yii\helpers\Url::toRoute(['viewrule', 'id' => $key]);                       
                  	//  		return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',$url,[
                   	//			'title'=>'View Rule',
                   	//			'data-method' => 'post',
                   	//			'data' => ['child'=> $model->name],
                    //    ]);
                  	//
                  	//},
                  	//'delete' => function ($url, $model, $key) {
                  	//  		$url = \yii\helpers\Url::toRoute(['deleterule', 'name' => $key]);                       
                  	//  		return Html::a('<span class="glyphicon glyphicon-remove"></span>',$url,[
                   	//			'title'=>'Delete Rule',
                   	//			'data-confirm' => Yii::t('yii', 'Are you sure you want to delete the rule?'),
                    //    ]);
                  	//
                  	//},
                  	'manage' => function ($url, $model, $key) {
                  	  		$url = \yii\helpers\Url::toRoute(['managerule', 'id' => $key]);                       
                  	  		return Html::a('<span class="glyphicon glyphicon-edit"></span>',$url,[
                   				'title'=>'Manage Rule',
                   				'data' => ['child'=> $model->name],
                        ]);
                  	
                  	}
                  ],
            ], 		
        ],
    ]); ?>
</div><!-- /.box body -->
    				</div><!-- /.box -->
    			</div><!-- /.row -->
    		</div><!-- /.column -->
    	</section><!-- /.section -->
</div>
