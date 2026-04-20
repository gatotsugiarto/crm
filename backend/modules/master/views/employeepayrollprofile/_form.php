<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Batch Employee Profile' : 'Edit Employee Profile';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';
?>

<style>
    .select2-rows-5 .select2-results__options { 
        max-height: calc(5 * 28px); /* 5 rows × tinggi per item */ 
        overflow-y: auto; 
    }
    .select2-selection__rendered { 
        white-space: normal !important; 
        word-break: break-word; 
    }
</style>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to manage employees profile.'
                : 'Update employee profile information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'employeepayrollprofile-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['salary/validate'],
    'action' => $isNew ? ['employeepayrollprofile/create'] : ['employeepayrollprofile/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <?php if($isNew): ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'profile_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\PayrollProfile::dropdown(),
                    'options' => [
                        'placeholder' => 'Payroll Profile',
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

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'employee_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\Employee::dropdown(),
                    'options' => [
                        'placeholder' => 'Employee',
                        // 'id' => 'employee_id',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                        'dropdownCssClass' => 'select2-rows-5', // custom class
                            'templateSelection' => new \yii\web\JsExpression(" function (data, container) { if (!data.id) return data.text; return data.text; } ")
                    ],
                ]) ?>
            </div>
        </div>
        
        
        <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'employee_id')->hiddenInput()->label(false) ?>    
                <label>Employee</label>
                <div><?= Html::encode($model->employee->fullname) ?></div>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'profile_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\PayrollProfile::dropdown(),
                    'options' => [
                        'placeholder' => 'Payroll Profile',
                        // 'id' => 'payroll_item_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        
        <?php endif; ?>

        <?= $form->field($model, 'status_id')->hiddenInput()->label(false) ?>

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
    
