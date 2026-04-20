<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Category' : 'Edit Category';
$icon = $isNew ? 'fa-plus' : 'fa-edit';
?>

<div class="modal-header bg-white border-0 pt-4 pb-3">
    <div>
        <h5 class="text-primary fw-bold mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <p class="text-muted pb-2 mb-0 border-bottom">
            <?= $isNew 
                ? 'Please fill in the form below to register a new category.' 
                : 'Update category information below.' ?>
        </p>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'payroll-category-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['payrollcategory/validate'],
    'action' => $isNew 
        ? ['payrollcategory/create'] 
        : ['payrollcategory/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'code')->textInput([
                    'placeholder' => 'Enter Code',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput([
                    'placeholder' => 'Enter Category Name',
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
                &nbsp;
            </div>
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
    
