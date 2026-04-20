<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Create Permission ( ' . $group . ' )';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
	<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">
    <div>
        <?= Html::a('<span class="glyphicon glyphicon-list-alt"></span> Backend', ['/auth/rbac/createpermission', 'group' => 'backend' ],
                    ['class' => 'btn btn-primary', 'style' => 'width:150px']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-list"></span> Frontend', ['/auth/rbac/createpermission', 'group'=>'frontend' ],
                    ['class' => 'btn btn-primary', 'style' => 'width:150px']) ?>
				<?= Html::a('<span class="glyphicon glyphicon-step-backward"></span> Cancel', ['/auth/rbac/permission'],
                    ['class' => 'btn btn-danger', 'style' => 'width:150px']) ?>                    
    </div>
		<br/>
    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
    <?php foreach ($routes as $modules => $controller)
                {
                ?>
    <div class="row">
            <div class="col-xs-12">
              
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Module Name: <?php echo $modules?></h3>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <tr>
                      <th>#</th>
                      <th>Permission</th>
                      <th>Controller/Action</th>
                      <th>Description</th>
                    </tr>
                    <?php foreach ($controller['controllers'] as $controller_name => $cont){
                    $permission_name =  $group.'.'.$modules.'.'.$controller_name.'.*';
                    if($controller_name != 'default'){
                    ?>
                    <tr>
                      <td>
                      	<?php if ( empty($permissions[$permission_name]) ) {?>
                      	<input type="checkbox" name="formpermission[<?php echo $permission_name ?>]" value="1">
                      	<?php } ?>
                      </td>
                      <td><?php echo $permission_name?></td>
                      <td><span class="label label-primary"><?php echo $controller_name ?></span></td>
                      <td><?php echo $cont['path']?></td>
                    </tr>
                    <?php foreach ($cont['action'] as $act) {
                    $permission_name =  $group.'.'.$modules.'.'.$controller_name.'.'.strtolower($act['name']);
                    ?>
                    <tr>
                      <td>
                      <?php if ( empty($permissions[$permission_name]) ) {?>
                      	<input type="checkbox" name="formpermission[<?php echo $permission_name ?>]" value="1">
                      <?php } ?>
                      </td>
                      <td><?php echo $permission_name ?></td>
                      <td><span class="label label-warning"><?php echo $act['name']?></span></td>
                      <td>&nbsp;</td>
                    </tr>
                    <?php }}} ?>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
              
            </div>
          </div>
    <?php } ?>
    	<div>
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-check"></span> Generate Permission</button>
            </div>
        <?php ActiveForm::end(); ?>
    	</div>
    
    <!-- /.box body -->
    				</div><!-- /.box -->
    			</div><!-- /.row -->
    		</div><!-- /.column -->
    	</section><!-- /.section -->
</div>
