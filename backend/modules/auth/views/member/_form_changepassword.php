<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$user = $model->getUser();
?>

<div class="change-password-form">
    <div class="mb-0 text-primary fw-bold"><i class="fa fa-key"></i> Change Password</div>
    <small class="text-muted">Please fill in the form below to change your password.</small>

    <?php $form = ActiveForm::begin([
        'id' => 'changepassword-form',
        'enableAjaxValidation' => true,
        'validationUrl' => ['user/validatechangepassword'],
        'action' => ['user/changepassword'],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= Html::label('Full Name', null, ['class' => 'form-label fw-bold']) ?>
            <input type="text" class="form-control" value="<?= Html::encode($user->fullname) ?>" readonly>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'old_password')->passwordInput(['placeholder' => 'Enter current password']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'new_password')->passwordInput(['placeholder' => 'Enter new password']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'confirm_password')->passwordInput(['placeholder' => 'Re-enter new password']) ?>
        </div>
    </div>

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


