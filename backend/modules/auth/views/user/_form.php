<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$isNew = $model->isNewRecord;
$title = $isNew ? 'New User Access' : 'Edit User Access';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new user.'
                : 'Update user information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'user-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['user/validate'],
    'action' => $isNew ? ['user/create'] : ['user/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
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

            <div class="col-md-6">
                <?php if ($isNew): ?>
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                <?php else: ?>
                    <?= $form->field($model, 'password')->hiddenInput()->label(false) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php $model->role = (!$isNew && $basicRole) ? $basicRole : null; ?>
                <?= $form->field($model, 'role')->widget(Select2::classname(), [
                    'data' => \common\modules\auth\models\AuthItem::dropdownBasic(),
                    'options' => [
                        'placeholder' => 'Basic Role',
                        // 'id' => 'payroll_item_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-3">
    <?= Html::button('<i class="fa fa-times"></i> Cancel', [
        'class' => 'btn btn-outline-secondary mr-2 px-4',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>

    <?= Html::submitButton('<i class="fa fa-save"></i> Save', [
        'class' => 'btn btn-primary px-4',
        'style' => 'min-width:140px;',
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
    
