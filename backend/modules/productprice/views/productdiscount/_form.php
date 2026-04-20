<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;
use kartik\datecontrol\DateControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Add Discount' : 'Edit Discount';
$icon  = $isNew ? 'fa-tag' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductDiscount $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Fill in the details below to add a new discount rule.'
                : 'Update the discount rule information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id'     => 'productdiscount-form',
    'action' => $isNew
        ? ['productdiscount/create', 'price_list_id' => $model->price_list_id]
        : ['productdiscount/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <?= Html::hiddenInput('form_token', $formToken) ?>

        <!-- Price List + Product -->
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'price_list_id')->widget(Select2::class, [
                    'data' => \common\modules\productprice\models\PriceList::dropdown(),
                    'options' => ['placeholder' => 'Select Price List'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'product_id')->widget(Select2::class, [
                    'data' => \common\modules\productprice\models\Product::dropdown(),
                    'options' => ['placeholder' => 'All Products (leave empty for all)'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ])->hint('Leave empty to apply discount to all products in this price list.') ?>
            </div>
        </div>

        <!-- Discount Type + Value -->
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'discount_type')->dropDownList([
                    'percent' => 'Percent (%)',
                    'amount'  => 'Fixed Amount',
                ], ['prompt' => '-- Select Type --']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'discount_value')->widget(NumberControl::class, [
                    'maskedInputOptions' => ['allowMinus' => false],
                    'options' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Discount Value',
                    ],
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'priority')->textInput([
                    'type'        => 'number',
                    'min'         => 1,
                    'placeholder' => 'Discount Priority',
                ]) ?>
            </div>
        </div>

        <!-- Min Qty + Valid From + Valid To -->
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'min_qty')->textInput([
                    'type'        => 'number',
                    'min'         => 1,
                    'placeholder' => 'Minimum Quantity',
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'valid_from')->widget(DateControl::class, [
                    'type'          => DateControl::FORMAT_DATE,
                    'saveFormat'    => 'php:Y-m-d',
                    'displayFormat' => 'php:d-m-Y',
                    'options'       => ['placeholder' => 'Valid From'],
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'valid_to')->widget(DateControl::class, [
                    'type'          => DateControl::FORMAT_DATE,
                    'saveFormat'    => 'php:Y-m-d',
                    'displayFormat' => 'php:d-m-Y',
                    'options'       => ['placeholder' => 'Valid To'],
                ]) ?>
            </div>
        </div>

        <!-- Is Stackable -->
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'is_stackable')->checkbox([
                    'label'         => 'Stackable — this discount can be combined with other discounts',
                    'uncheck'       => 0,
                    'labelOptions'  => ['class' => 'text-muted small'],
                ]) ?>
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