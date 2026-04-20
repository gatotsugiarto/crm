<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Roles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
	<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Create Role', ['createrole'], ['class' => 'btn btn-primary', 'style' => 'width:150px']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'description:ntext',
            'rule_name',
            //'data:ntext',
            //'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn', 
            	'template' => '{view} {update} {delete} {manage}',
            			'buttons'=>[
                  	'view' => function ($url, $model, $key) {     
                  	  		$url = \yii\helpers\Url::toRoute(['viewrole', 'id' => $key]);                       
                  	  		return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',$url,[
                   				'title'=>'View Role',
                   				'data-method' => 'post',
                   				'data' => ['child'=> $model->name],
                        ]);
                  	
                  	},
                  	'update' => function ($url, $model, $key) {     
                  	  		$url = \yii\helpers\Url::toRoute(['updaterole', 'id' => $key]);                       
                  	  		return Html::a('<span class="glyphicon glyphicon-pencil"></span>',$url,[
                   				'title'=>'Update Role',
                   				'data-method' => 'post',
                   				'data' => ['child'=> $model->name],
                        ]);
                  	
                  	},
                  	'delete' => function ($url, $model, $key) {
                  	  		$url = \yii\helpers\Url::toRoute(['deleterole', 'name' => $key]);                       
                  	  		return Html::a('<span class="glyphicon glyphicon-remove"></span>',$url,[
                   				'title'=>'Delete Role',
                   				'data-confirm' => Yii::t('yii', 'Are you sure you want to delete the role?'),
                        ]);
                  	
                  	},
                  	'manage' => function ($url, $model, $key) {
                  	  		$url = \yii\helpers\Url::toRoute(['managerole', 'id' => $key]);                       
                  	  		return Html::a('<span class="glyphicon glyphicon-edit"></span>',$url,[
                   				'title'=>'Manage Role',
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
