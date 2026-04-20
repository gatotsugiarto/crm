<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Opportunity Product' : 'Edit Opportunity Product';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunityProduct $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new opportunity product.'
                : 'Update opportunity product information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'opportunityproduct-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['opportunityproduct/validate'],
    'action' => $isNew ? ['opportunityproduct/create'] : ['opportunityproduct/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
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

            <div class="col-md-6">
                <?= $form->field($model, 'product_id')->widget(Select2::class, [
                    'data' => \common\modules\productprice\models\Product::dropdown(),
                    'options' => [
                        'placeholder' => 'Select Product',
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
                <?= $form->field($model, 'qty')->textInput([
                    'type'        => 'number',
                    'min'         => 1,
                    'placeholder' => 'Quantity',
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'price')->widget(NumberControl::class, [
                    'maskedInputOptions' => ['allowMinus' => false],
                    'options' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Price',
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'discount')->widget(NumberControl::class, [
                    'maskedInputOptions' => ['allowMinus' => false],
                    'options' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Discount Value',
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'total')->hiddenInput()->label(false) ?>
                
                <div class="form-group">
                    <label class="form-control-label d-block">Is Upsell?</label>

                    <div class="custom-control custom-switch">
                        <input type="hidden" name="OpportunityProduct[is_upsell]" value="0">

                        <input type="checkbox"
                               id="is_upsell_switch"
                               name="OpportunityProduct[is_upsell]"
                               value="1"
                               class="custom-control-input"
                               <?= $model->is_upsell ? 'checked' : '' ?>>

                        <label class="custom-control-label" for="is_upsell_switch">
                            No / Yes
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'parent_product_id')->widget(Select2::classname(), [
                    'data' => \common\modules\sales\models\OpportunityProduct::dropdown(),
                    'options' => [
                        'placeholder' => 'Parent Product',
                        // 'id' => 'status_id',
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
