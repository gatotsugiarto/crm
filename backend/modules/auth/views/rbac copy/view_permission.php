<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthItem */

$this->title = 'Detail Permission';
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['permission']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-view">
<section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-body">

    <p>
        <?= Html::a('Delete', ['deletepermission', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Cancel', ['/auth/rbac/permission'],
                    ['class' => 'btn btn-info']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'type',
            'description:ntext',
            'rule_name',
            'data:ntext',
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
