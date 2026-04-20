<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\auth\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Assignment';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'id',
            'username',
            'password_reset_token',
            // 'email:email',
            // 'status',
            'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn', 
            	'template' => '{update}',
            			'buttons'=>[
                  	'update' => function ($url, $model, $key) {
                  	  		$url = \yii\helpers\Url::toRoute(['manageassignment', 'id' => $key]);                       
                  	  		return Html::a('<span class="glyphicon glyphicon-edit"></span>',$url,[
                   				'title'=>'User Assignment',
                   				'data' => ['child'=> $model->id],
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
