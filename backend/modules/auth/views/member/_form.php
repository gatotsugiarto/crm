<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Member' : 'Edit Member';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <div class="mb-0 text-primary fw-bold"><i class="fa <?= $icon ?>"></i> <?= $title ?></div>
        <small class="text-muted"><?= $isNew ? 'Please fill in the form below to register a new member.' : 'Update member information below.' ?></small>
    </div>
</div>

<div class="modal-body p-4">
    <?php $form = ActiveForm::begin([
        'id' => 'member-form',
        'enableAjaxValidation' => true,
        'validationUrl' => ['member/validate'],
        'action' => $model->isNewRecord ? ['member/create'] : ['member/update', 'id' => $model->id],
        'options' => ['data-pjax' => 0],
    ]); ?>

    <?= Html::hiddenInput('form_token', $formToken) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'fullname')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>

        <?php if ($model->isNewRecord): ?>
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <?php else: ?>
        <?= $form->field($model, 'password')->hiddenInput()->label(false) ?>
        <?php endif; ?>
    </div>

    <?php if ($model->isNewRecord): ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'company_id')->textInput() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'client_id')->textInput() ?>
        </div>
    </div>
    <?php else: ?>
    <?= $form->field($model, 'client_id')->hiddenInput()->label(false) ?>
    <?php endif; ?>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <?= Html::button('<i class="fa fa-times"></i> Cancel', [
            'class' => 'btn btn-warning',
            'data-bs-dismiss' => 'modal',
        ]) ?>
        <?= Html::submitButton('<i class="fa fa-save"></i> Submit', [
            'class' => 'btn btn-primary',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
