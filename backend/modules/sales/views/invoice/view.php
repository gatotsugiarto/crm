<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Invoice $model */


$this->title = 'Detail '.'Invoices';
$sub_title = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?= $this->title ?>
    </h5>
    <p class="text-muted small mb-0">
        <?=$sub_title ?>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Invoice number</span><br>
                <span><small><?= Html::encode($model->invoice_number) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Account</span><br>
                <span><small><?= Html::encode($model->account_id) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Sales order</span><br>
                <span><small><?= Html::encode($model->sales_order_id) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Invoice date</span><br>
                <span><small><?= Html::encode($model->invoice_date) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Due date</span><br>
                <span><small><?= Html::encode($model->due_date) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Total amount</span><br>
                <span><small><?= Html::encode($model->total_amount) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <span><small><?= Html::encode($model->status) ?></small></span>
            </div>

        </div>


        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Created by:
                <strong><?= Html::encode($model->createdBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->created_at) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="fa fa-edit"></i> Updated by:
                <strong><?= Html::encode($model->updatedBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?=Html::encode($model->updated_at) ?></small>
            </div>
        </div>

    </div>
</div>

<div class="text-end mt-3">
<?php if (Yii::$app->request->isAjax): ?>

    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>

<?php else: ?>

    <?= Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
        'class' => 'btn btn-outline-secondary',
        'style' => 'min-width:140px;',
    ]) ?>

<?php endif; ?>
</div>
