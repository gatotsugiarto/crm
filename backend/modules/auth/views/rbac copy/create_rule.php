<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthItem */

$this->title = 'Create Rule';
$this->params['breadcrumbs'][] = ['label' => 'Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">
	<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">

    <?= $this->render('_form_rule', [
        'model' => $model,
        //'roles' => $roles,
    ]) ?>
</div><!-- /.box body -->
    				</div><!-- /.box -->
    			</div><!-- /.row -->
    		</div><!-- /.column -->
    	</section><!-- /.section -->
</div>
