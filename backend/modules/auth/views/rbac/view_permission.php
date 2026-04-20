<?php
use yii\helpers\Html;
?>

<div class="mb-3">
    <div class="mb-0 text-primary fw-bold"><i class="fa fa-user-circle"></i> Detail Permission</div>
    <small class="text-muted">This section provides detailed information about the selected permission, name, type, description and creation/update history.</small>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Permission Name:</strong><br>
        <?= Html::encode($model->name) ?>
    </div>
    <div class="col-md-6">
        <strong>Type:</strong><br>
        <?= Html::encode($model->type == 1 ? 'Role' : 'Permission') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Description:</strong><br>
        <?= Html::encode($model->description) ?>
    </div>
    <div class="col-md-6">
        <strong>Rule Name:</strong><br>
        <?= Html::encode($model->rule_name) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Created:</strong><br>
        <?= date('Y-m-d H:i:s', Html::encode($model->created_at)) ?>
    </div>
    <div class="col-md-6">
        <strong>Updated:</strong><br>
        <?= date('Y-m-d H:i:s', Html::encode($model->updated_at)) ?>
    </div>
</div>

<div class="text-end mt-3">
    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-secondary',
        'data-bs-dismiss' => 'modal',
    ]) ?>
</div>
