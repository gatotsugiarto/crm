<?php
use yii\helpers\Html;

$this->title = "Detail Employee Salary";
$sub_title = "View detailed information and related records.";
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?=$this->title ?>
    </h5>
    <p class="text-muted small mb-0">
        <?=$sub_title ?>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Employee</span><br>
                <span class="fw-semibold"><?= Html::encode($model->employee->fullname) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Payroll Component</span><br>
                <span class="fw-semibold"><?= Html::encode($model->payrollItem->name) ?>
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Salary Type</span><br>
                <span class="fw-semibold"><?= Html::encode($model->payrollItem->salary_type) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Amount</span><br>
                <span class="fw-semibold">Rp. <?= number_format($model->amount, 2, ',', '.')?>
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Insert From</span><br>
                <span class="fw-semibold"><?= Html::encode($model->insert_by) ?></span>
            </div>
            <div class="col-md-6"></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <span class="fw-semibold"><?= Html::encode($model->status->status_active) ?></span>
            </div>
            <div class="col-md-6">
                <?php
                if($model->is_processed == 1){
                    $processed_at = ', '.$model->processed_at;
                }else{
                    $processed_at = '';
                }
                ?>
                <span class="text-secondary small">Payroll Processed</span><br>
                <span class="fw-semibold"><?= Html::encode($model->processed->status) ?><?= Html::encode($processed_at) ?>
                </span>
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
                <i class="far fa-clock"></i> <small><?= Html::encode($model->updated_at) ?></small>
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
