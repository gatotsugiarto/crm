<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Role' : 'Edit Role';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <div class="mb-0 text-primary fw-bold"><i class="fa <?= $icon ?>"></i> <?= $title ?></div>
        <small class="text-muted"><?= $isNew ? 'Please fill in the form below to register a new company.' : 'Update company information below.' ?></small>
    </div>
</div>

<style>
    .select2-container { z-index: 9999 !important; }
.select2-dropdown { z-index: 99999 !important; }

</style>


    <?php $form = ActiveForm::begin([
        'id' => 'role-form',
        'enableAjaxValidation' => true,
        'validationUrl' => ['rbac/validate'],
        'action' => $model->isNewRecord ? ['rbac/createrole'] : ['rbac/updaterole', 'id' => $model->name],
        'options' => ['data-pjax' => 0],
    ]); ?>

    <?= Html::hiddenInput('form_token', $formToken) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'description')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'data')->widget(\kartik\select2\Select2::classname(), [
                'data' => [
                    1 => 'No',
                    2 => 'Yes',
                ],
                'options' => [
                    'placeholder' => 'Select action...',
                    'id' => 'status_id',
                    'multiple' => false,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'dropdownParent' => new \yii\web\JsExpression('$("#formModal")'), // pastikan ID sesuai modal
                    'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    'templateResult' => new \yii\web\JsExpression('function (data) {
                        if (!data.id) { return data.text; }
                        if (data.id == 1) {
                            return $("<span><i class=\'fa fa-check-circle text-success\'></i> " + data.text + "</span>");
                        } else {
                            return $("<span><i class=\'fa fa-ban text-danger\'></i> " + data.text + "</span>");
                        }
                    }'),
                    'templateSelection' => new \yii\web\JsExpression('function (data) {
                        return data.text;
                    }'),
                ],
            ])->label('Showing At Registration?') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'type')->hiddenInput(['value' => 1])->label(false) ?>
        </div>
    </div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <!-- LEFT: Back / Close -->
    <div>
        <?php if (Yii::$app->request->isAjax): ?>

            <?= Html::button('<i class="fa fa-times"></i> Close', [
                'class' => 'btn btn-outline-secondary px-4',
                'data-dismiss' => 'modal',
                'style' => 'min-width:140px;',
            ]) ?>

        <?php else: ?>

            <?= Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
                'class' => 'btn btn-outline-secondary px-4',
                'style' => 'min-width:140px;',
            ]) ?>

        <?php endif; ?>
    </div>

    <!-- RIGHT: Submit -->
    <div>
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', [
            'class' => 'btn btn-primary px-4',
            'style' => 'min-width:140px;',
        ]) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>

