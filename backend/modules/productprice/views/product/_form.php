<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Product' : 'Edit Product';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new product.'
                : 'Update product information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'product-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['product/validate'],
    'action' => $isNew ? ['product/create'] : ['product/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'category_id')->widget(Select2::class, [
                    'data' => \common\modules\productprice\models\ProductCategory::dropdown(),
                    'options' => [
                        'placeholder' => 'Product Category',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'uom_id')->widget(Select2::class, [
                    'data' => \common\modules\productprice\models\ProductUom::dropdown(),
                    'options' => [
                        'placeholder' => 'UOM',
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
                <?= $form->field($model, 'type')->widget(Select2::class, [
                    'data' => [ 'Goods' => 'Goods', 'Service' => 'Service', 'Subscription' => 'Subscription', 'Bundle' => 'Bundle', 'Software' => 'Software', ],
                    'options' => [
                        'placeholder' => 'Product Type',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'bundle_price_type')->widget(Select2::class, [
                    'data' => [ 'fixed' => 'Fixed', 'sum' => 'Sum', ],
                    'options' => [
                        'placeholder' => 'Bundle Price Type',
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
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'base_price')->widget(NumberControl::classname(), [
                    'maskedInputOptions' => ['allowMinus' => false],
                    'options' => ['class' => 'form-control', 'placeholder' => 'Base Price'],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'is_bundle_expand')->checkbox([
                    'label'         => 'Bundle Expand — If enabled, individual items within this bundle will be listed separately on invoices and reports',
                    'uncheck'       => 0,
                    'labelOptions'  => ['class' => 'text-muted small'],
                ]) ?>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'status_id')->hiddenInput()->label(false) ?>
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
