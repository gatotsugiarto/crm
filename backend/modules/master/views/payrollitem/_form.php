<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Component' : 'Edit Component';
$icon = $isNew ? 'fa-plus' : 'fa-edit';
?>

<div class="modal-header bg-white border-0 pt-4 pb-3">
    <div>
        <h5 class="text-primary fw-bold mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <p class="text-muted pb-2 mb-0 border-bottom">
            <?= $isNew 
                ? 'Please fill in the form below to register a new component.' 
                : 'Update component information below.' ?>
        </p>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'payroll-item-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['payrollitem/validate'],
    'action' => $isNew 
        ? ['payrollitem/create'] 
        : ['payrollitem/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <?= Html::hiddenInput('form_token', $formToken) ?>
        <?= Html::hiddenInput('is_reprocessable', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'code')->textInput([
                    'placeholder' => 'Enter Code',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput([
                    'placeholder' => 'Enter Component Name',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'salary_type')->dropDownList([ 'RECURRING' => 'RECURRING', 'ONETIME' => 'ONETIME', ], ['prompt' => '']) ?>
            </div>

            <div class="col-md-6">
                <?=$form->field($model, 'category_id')->widget(Select2::classname(), [
                    'data' => common\modules\master\models\PayrollCategory::dropdown(),
                    'options' => [
                        'placeholder' => 'Payroll category...',
                        //'id' => 'year',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'type')->dropDownList([ 'DATA' => 'DATA', 'RATE' => 'RATE', 'FORMULA' => 'FORMULA', 'SUMMARY' => 'SUMMARY', ], ['prompt' => '']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'sign')->dropDownList([ 'PLUS' => 'PLUS', 'MINUS' => 'MINUS', 'NONE' => 'NONE', ], ['prompt' => '']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6"></div>

            <div class="col-md-6">
                <?= $form->field($model, 'sign2')->dropDownList([ 'MULTIPLY' => 'MULTIPLY', 'DEVIDE' => 'DEVIDE', 'NONE' => 'NONE', ], ['prompt' => '']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'default_value')->textInput([
                    'placeholder' => 'Enter Default Value',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'taxable')->dropDownList([ 1 => 'Yes', 2 => 'No', ], ['prompt' => '']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'percent')->textInput([
                    'placeholder' => 'Enter Percentage',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'cap')->textInput([
                    'placeholder' => 'Enter Capping / Maximum Cap',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'item_code')->textInput([
                    'placeholder' => 'Var Component Code',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'base_multiplier')->textInput([
                    'placeholder' => 'Enter Base Multiplier',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'display_order')->textInput([
                    'placeholder' => 'Enter Display Order',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'monthly_exec')->textInput([
                    'placeholder' => 'Month Execute',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'slip_display')->dropDownList([ 'Y' => 'Yes', 'N' => 'No', ], ['prompt' => '']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'slip_position')->dropDownList([ 'C' => 'Credit', 'D' => 'Debit', 'S' => 'Summary', ], ['prompt' => '']) ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'tax_nature')->dropDownList([ 'TERATUR' => 'TERATUR', 'TIDAK_TERATUR' => 'TIDAK_TERATUR', ], ['prompt' => '']) ?>
            </div>

            <div class="col-md-6"></div>
        </div>

        
    </div>
</div>
<div class="d-flex justify-content-end mt-4">
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
    
