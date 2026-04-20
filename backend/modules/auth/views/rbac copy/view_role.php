<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthItem */

$this->title = 'Detail Role';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['role']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-view">
<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">

    <p>
                    
       	<?= Html::a('<span class=\'glyphicon glyphicon-edit\'></span> Update', ['updaterole', 'id' => $model->name], ['class' => 'btn btn-primary', 'style' => 'width:150px']) ?>
    		<?= Html::a('<span class=\'glyphicon glyphicon-remove\'></span> Delete', ['deleterole', 'name' => $model->name], [
    		    'class' => 'btn btn-danger',
    		    'style' => 'width:150px',
    		    'data' => [
    		        'confirm' => 'Are you sure you want to delete this item?',
    		        'method' => 'post',
    		    ],
    		]) ?>
    		<?= Html::a('<span class="glyphicon glyphicon-step-backward"></span> Home', ['/auth/rbac/role'],['class' => 'btn btn-info', 'style' => 'width:150px']) ?>             
                    
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            //'type',
            'description:ntext',
            'rule_name',
            [ 
                'attribute' => 'data',
                'value' => $model->data == 1 ? 'Not show at registration' : 'Will be show at registration',
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>
</div><!-- /.box body -->
    				</div><!-- /.box -->
    			</div><!-- /.row -->
    		</div><!-- /.column -->
    	</section><!-- /.section -->
</div>
