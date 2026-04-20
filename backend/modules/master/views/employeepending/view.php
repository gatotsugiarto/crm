<?php
use yii\helpers\Html;

$this->title = "Detail Employee Pending";
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
                <span class="text-secondary small">Pending Type / Pending Date</span><br>
                <span class="fw-semibold"><?= Html::encode($model->pending_type) ?> / <?= Html::encode($model->pending_date) ?>
                </span>
            </div>
        </div>

        <?php
        if($model->pending_type != 'PAYROLL'){
        ?>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Marital-Family Status</span><br>
                <span class="fw-semibold"><?= Html::encode($model->marital_status) ?> - <?= Html::encode($model->family_status) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">PTKP - Cut-off January</span><br>
                <span class="fw-semibold"><?= Html::encode($model->ptkp) ?>
                </span>
            </div>
        </div>

        <?php
        }
        ?>

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
