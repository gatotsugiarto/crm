<?php
use yii\helpers\Html;

$this->title = "Detail Role";
$sub_title = "This section provides detailed information about the selected role, name, type, description and creation/update history.";
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
                <span class="text-secondary small">Role Name</span><br>
                <span class="fw-semibold"><?= Html::encode($model->name) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Type</span><br>
                <span class="fw-semibold"><?= Html::encode($model->type == 1 ? 'Role' : 'Permission') ?></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Description</span><br>
                <span class="fw-semibold"><?= Html::encode($model->data == 1 ? 'Not show at registration' : 'Will be show at registration') ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Rule Name</span><br>
                <span class="fw-semibold"><?= Html::encode($model->rule_name) ?></span>
            </div>
        </div>

        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="far fa-clock"></i> <small><?= date('Y-m-d H:i:s', Html::encode($model->created_at)) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="far fa-clock"></i> <small><?= date('Y-m-d H:i:s', Html::encode($model->updated_at)) ?></small>
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