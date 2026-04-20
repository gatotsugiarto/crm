<?php
use yii\helpers\Html;

/** @var $model \common\models\User */
?>

<div class="mb-3">
    <div class="mb-0 text-primary fw-bold page-title">
        <i class="fa fa-user-circle"></i>&nbsp;&nbsp;&nbsp;My Profile
    </div>
    <small class="text-muted">
        Here you can view your complete profile information such as username, full name, email, status, and registration details.
    </small>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Username</span><br>
                <?= Html::encode($model->username) ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Fullname</span><br>
                <?= Html::encode($model->fullname) ?>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Email</span><br>
                <?= Html::encode($model->email) ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Created At</span><br>
                <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i:s') ?>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <?= Html::tag('span', $model->status ? 'Active' : 'Non Active', [
                    'class' => $model->status ? 'badge badge-success' : 'badge badge-secondary'
                ]) ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Updated At</span><br>
                <?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i:s') ?>
            </div>
        </div>
    </div>
</div>
<div class="text-end mt-3">
    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>
</div>


