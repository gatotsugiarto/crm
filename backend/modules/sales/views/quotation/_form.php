<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Quotation' : 'Edit Quotation';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Quotation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new quotation.'
                : 'Update quotation information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'quotation-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['quotation/validate'],
    'action' => $isNew ? ['quotation/create'] : ['quotation/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'quotation_number')->textInput([
                    'maxlength' => true,
                    'readonly' => true,
                    'disabled' => true,
                    'value' => $model->isNewRecord ? $model->generateQuotationNumberPreview() : $model->quotation_number,
                ]) ?>
            </div>

            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'account_id')->widget(Select2::classname(), [
                    'data' => \common\modules\sales\models\Account::dropdown(),
                    'options' => [
                        'placeholder' => 'Account Name',
                        'id' => 'account_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'opportunity_id')->widget(Select2::class, [
                    'data' => \common\modules\sales\models\Opportunity::dropdown(),
                    'options' => [
                        'placeholder' => 'Select Opportunity',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'quotation_date')->widget(\kartik\date\DatePicker::classname(), [
                    'options' => ['placeholder' => 'Quotation Date'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'valid_until')->widget(\kartik\date\DatePicker::classname(), [
                    'options' => ['placeholder' => 'Valid Until'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'total_amount')->widget(NumberControl::class, [
                    'maskedInputOptions' => [
                        'allowMinus' => false,
                        'prefix' => 'Rp ',
                        'groupSeparator' => '.',
                        'radixPoint' => ',',
                    ],
                    'displayOptions' => [
                        'readonly' => true,
                        'class' => 'form-control',
                    ],
                    'options' => [
                        'value' => $model->total_amount ?? 0,
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?php
                if($isNew){
                    $model->status = 'Draft';
                }
                ?>
                <?= $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => [ 
                        'Draft' => 'Draft', 
                        'Sent' => 'Sent', 
                        'Approved' => 'Approved', 
                        'Rejected' => 'Rejected', 
                    ],
                    'options' => [
                        'placeholder' => 'Status',
                        'id' => 'status',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <?php if(!$isNew): ?>
                    <?= $form->field($model, 'status_id')->widget(Select2::classname(), [
                        'data' => \common\modules\master\models\StatusActive::dropdown(),
                        'options' => [
                            'placeholder' => 'Status',
                            // 'id' => 'status_id',
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                            // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                        ],
                    ]) ?>
                <?php else: ?>
                    <?= $form->field($model, 'status_id')->hiddenInput()->label(false) ?>
                <?php endif; ?>
            </div>
            <div class="col-md-6"></div>
        </div>

    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <!-- LEFT: Back / Close -->
    <div>
        <?php  if (Yii::$app->request->isAjax): ?>

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
