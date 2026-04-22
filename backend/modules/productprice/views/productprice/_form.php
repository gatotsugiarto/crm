<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Add Product Price' : 'Edit Product Price';
$icon  = $isNew ? 'fa-plus-circle' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductPrice $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Fill in the form below to add a product price.'
                : 'Update product price information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id'     => 'productprice-form',
    'action' => $isNew
        ? ['/productprice/productprice/create', 'price_list_id' => $model->price_list_id]
        : ['/productprice/productprice/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <?= Html::hiddenInput('form_token', $formToken) ?>

        <!-- PriceList -->
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'price_list_id')->widget(Select2::class, [
                    'data' => \common\modules\productprice\models\PriceList::dropdown(),
                    'options' => [
                        'placeholder' => 'Select Price List',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        <!-- Product -->
        <div class="row">
            <div class="col-md-12">
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

        <!-- Price + Min Qty -->
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'price')->widget(NumberControl::class, [
                    'maskedInputOptions' => ['allowMinus' => false],
                    'options' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Price',
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'min_qty')->textInput([
                    'type'        => 'number',
                    'min'         => 1,
                    'placeholder' => 'Minimum Quantity',
                ]) ?>
            </div>
        </div>

        <!-- Valid From + Valid To -->
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'valid_from')->textInput(['type' => 'date']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'valid_to')->textInput(['type' => 'date']) ?>
            </div>
        </div>

        <!-- Status (edit only) -->
        <div class="row">
            <div class="col-md-6">
                <?php if (!$isNew): ?>
                    <?= $form->field($model, 'status_id')->widget(Select2::class, [
                        'data' => \common\modules\master\models\StatusActive::dropdown(),
                        'options' => [
                            'placeholder' => 'Status',
                            'multiple'    => false,
                        ],
                        'pluginOptions' => ['allowClear' => true],
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

    <div>
        <?php if (Yii::$app->request->isAjax): ?>
            <?= Html::button('<i class="fa fa-times"></i> Close', [
                'class'        => 'btn btn-outline-secondary px-4',
                'data-dismiss' => 'modal',
                'style'        => 'min-width:140px;',
            ]) ?>
        <?php else: ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
                'class' => 'btn btn-outline-secondary px-4',
                'style' => 'min-width:140px;',
            ]) ?>
        <?php endif; ?>
    </div>

    <div>
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', [
            'class' => 'btn btn-primary px-4',
            'style' => 'min-width:140px;',
        ]) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>



