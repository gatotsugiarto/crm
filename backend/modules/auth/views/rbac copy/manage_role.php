<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\AuthItem */

$this->title = 'Update Role: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['role']];
$this->params['breadcrumbs'][] = 'Manage';
?>
<div class="auth-item-update">
	<!-- Main content -->
	<section class="content">
	  <div class="row">
	    <div class="col-md-12">
	
	      <div class="box box-primary">
	        <div class="box-header">
	          <h3 class="box-title">Roles</h3>
	        </div>
	        <div class="box-body">
	          
	         <?php if ( Yii::$app->session->hasFlash('error') ) { ?>
               <div class="alert alert-danger" role="alert">
                   <?= Yii::$app->session->getFlash('error'); ?>
               </div>
           <?php } ?>
	         	
	         	<?php $form = ActiveForm::begin(); ?>

    				<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'style'=>'width:300px','readonly' => true]) ?>
    				
    				<?= $form->field($model, 'type')->textInput(['style'=>'width:100px','readonly' => true]) ?>
    				
    				<?= $form->field($model, 'description')->textarea(['rows' => 6, 'style'=>'width:500px', 'readonly' => true]) ?>
    				
    				<?php ActiveForm::end(); ?>
	
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	
	    </div><!-- /.col (left) -->
	    <div class="col-md-6">
	
	      <div class="box box-info">
	        <div class="box-header">
	          <h3 class="box-title">Roles & Permissions Assigned</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_data_role', ['children' => $children,]) ?>
	        
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	
	    </div><!-- /.col (left) -->
	    <div class="col-md-6">
	      <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
	      <div class="box box-info">
	        <div class="box-header">
	          <h3 class="box-title">Other Roles</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_listroles', ['roles' => $allroles, 'assigned' => $assigned]) ?>
	
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	
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
