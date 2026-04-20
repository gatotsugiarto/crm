<?php
use yii\helpers\Html;

/** @var $model \common\models\User */
?>

<div class="mb-3">
    <div class="mb-0 text-primary fw-bold"><i class="fa fa-user-circle"></i> Detail Member</div>
    <small class="text-muted">The following section displays the complete information of the selected user, including
    username, fullname, email address, user status and registration details.</small>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Username:</strong><br>
        <?= Html::encode($model->username) ?>
    </div>
    <div class="col-md-6">
        <strong>Fullname:</strong><br>
        <?= Html::encode($model->fullname) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Company:</strong><br>
        <?= Html::encode($model->company->company) ?>
    </div>
    <div class="col-md-6">
        <strong>Client:</strong><br>
        <?= Html::encode($model->client->client) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Email:</strong><br>
        <?= Html::encode($model->email) ?>
    </div>
    <div class="col-md-6">
        <strong>Created At:</strong><br>
        <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i:s') ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <strong>Status:</strong><br>
        <?= Html::tag('span', $model->status ? 'Active' : 'Non Active', [
            'class' => $model->status ? 'badge bg-success' : 'badge bg-secondary'
        ]) ?>
    </div>
    <div class="col-md-6">
        <strong>Updated At:</strong><br>
        <?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i:s') ?>
    </div>
</div>

<div class="text-end mt-3">
    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-secondary',
        'data-bs-dismiss' => 'modal',
    ]) ?>
</div>
