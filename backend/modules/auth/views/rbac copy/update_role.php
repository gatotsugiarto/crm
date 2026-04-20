<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthItem */

$this->title = 'Update Role: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['role']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-item-update">
<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">

    <?= $this->render('_form_role', [
        'model' => $model,
    ]) ?>
</div><!-- /.box body -->
    				</div><!-- /.box -->
    			</div><!-- /.row -->
    		</div><!-- /.column -->
    	</section><!-- /.section -->
</div>
