<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\AuthItem */

$this->title = 'Update Rule: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Rules', 'url' => ['rule']];
$this->params['breadcrumbs'][] = 'Manage Rule';
?>
<div class="auth-item-update">
	<!-- Main content -->
	<section class="content">
	  <div class="row">
	    <div class="col-md-12">
	
	      <div class="box box-primary">
	        <div class="box-header">
	          <h3 class="box-title">Rules</h3>
	        </div>
	        <div class="box-body">
	          
	         <?php if ( Yii::$app->session->hasFlash('error') ) { ?>
               <div class="alert alert-danger" role="alert">
                   <?= Yii::$app->session->getFlash('error'); ?>
               </div>
           <?php } ?>
	         <?php $form = ActiveForm::begin(); ?>

    			<?= $form->field($model, 'name')->textInput(['maxlength' => 64, 'style'=>'width:300px', 'readonly' => true]) ?>
    
    			<?= $form->field($model, 'description')->textarea(['style'=>'width:550px', 'rows' => '6', 'readonly' => true]) ?>

			    <?php ActiveForm::end(); ?>
	
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	
	    </div><!-- /.col (left) -->
	    <div class="col-md-6">
	
	      <div class="box box-info">
	        <div class="box-header">
	          <h3 class="box-title">Rules & Permissions Assigned</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_data_rule', ['children' => $children,]) ?>
	        
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	
	    </div><!-- /.col (left) -->
	    <div class="col-md-6">
	      <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
	      <!-- iCheck -->
	      <div class="box box-info">
	        <div class="box-header">
	          <h3 class="box-title">Permissions</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_listpermission', ['permissions' => $allpermissions, 'assigned' => $assigned]) ?>
	          
	        </div><!-- /.box-body -->
	        <div class="box-footer">
	          More informations available. <a href="http://fronteed.com/iCheck/">Youtube Documentation</a>
	        </div>
	      </div><!-- /.box -->
	    	<?php ActiveForm::end(); ?>
	    </div><!-- /.col (right) -->
	  </div><!-- /.row -->
	
	</section><!-- /.content -->
</div>
