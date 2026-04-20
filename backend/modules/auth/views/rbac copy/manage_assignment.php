<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\user\models\AuthAssignment */

$this->title = 'User Assignment: '. $user->username;
$this->params['breadcrumbs'][] = ['label' => 'User Assignments', 'url' => ['assignment']];
//$this->params['breadcrumbs'][] = ['label' => $model->item_name, 'url' => ['view', 'item_name' => $model->item_name, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-assignment-update">
	<!-- Main content -->
	<section class="content">
	  <div class="row">
		<?php if ( Yii::$app->session->hasFlash('error') ) { ?>
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">
		    		<div class="alert alert-danger" role="alert">
		        	<?= Yii::$app->session->getFlash('error'); ?>
		    		</div>
		    	</div><!-- /.box-body -->
				</div><!-- /.box -->	
			</div>
		<?php } ?>
			
			<div class="col-md-6">
        <div class="box box-info">
	        <div class="box-header">
	          <h3 class="box-title">Roles & Permssions</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_data_assignment', ['children' => $children,]) ?>
	        
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	      
	      
	    </div><!-- /.col (left) -->
	    <div class="col-md-6">
	      <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
	      <div class="box box-primary">
	        <div class="box-header">
	          <h3 class="box-title">Other Roles</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_listroles', ['roles' => $allroles, 'assigned' => $assigned]) ?>
	
	        </div><!-- /.box-body -->
	      </div><!-- /.box -->
	
	      <!-- iCheck -->
	      <div class="box box-primary">
	        <div class="box-header">
	          <h3 class="box-title">Permission</h3>
	        </div>
	        <div class="box-body">
	          
	          <?= $this->render('_listpermission', ['permissions' => $allpermissions, 'assigned' => $assigned]) ?>
	          
	        </div><!-- /.box-body -->
	        
	      </div><!-- /.box -->
	    	<?php ActiveForm::end(); ?>
	    </div><!-- /.col (right) -->
	  </div><!-- /.row -->
	
	</section><!-- /.content -->

</div>
