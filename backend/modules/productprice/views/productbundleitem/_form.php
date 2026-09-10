<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Product Bundle' : 'Edit Product Bundle';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductBundleItem $model */
/** @var yii\widgets\ActiveForm $form */

$productDropdown = \common\modules\productprice\models\Product::dropdown();
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new product bundle. Add as many products as the bundle needs.'
                : 'Update product bundle item information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'productbundleitem-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['quotationitem/validate'],
    'action' => $isNew ? ['productbundleitem/create'] : ['productbundleitem/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'bundle_product_id')->widget(Select2::classname(), [
                    'data' => $productDropdown,
                    'options' => [
                        'placeholder' => 'Bundle Product',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]) ?>
            </div>
        </div>

        <?php if ($isNew): ?>

            <label class="control-label">Products in this Bundle</label>

            <div id="bundle-item-rows">
                <div class="row bundle-item-row align-items-start mb-2">
                    <div class="col-md-6">
                        <select class="form-control js-product-select" name="items[0][product_id]">
                            <option value=""></option>
                            <?php foreach ($productDropdown as $id => $name): ?>
                                <option value="<?= Html::encode($id) ?>"><?= Html::encode($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" min="1" class="form-control" name="items[0][quantity]" placeholder="Quantity">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger js-remove-row" disabled>
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="js-add-bundle-row" class="btn btn-outline-primary btn-sm mt-2 mb-3">
                <i class="fa fa-plus"></i> Add Product
            </button>

            <template id="bundle-row-template">
                <div class="row bundle-item-row align-items-start mb-2">
                    <div class="col-md-6">
                        <select class="form-control js-product-select" name="items[__INDEX__][product_id]">
                            <option value=""></option>
                            <?php foreach ($productDropdown as $id => $name): ?>
                                <option value="<?= Html::encode($id) ?>"><?= Html::encode($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" min="1" class="form-control" name="items[__INDEX__][quantity]" placeholder="Quantity">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger js-remove-row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>

        <?php else: ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'product_id')->widget(Select2::classname(), [
                        'data' => $productDropdown,
                        'options' => [
                            'placeholder' => 'Product',
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'quantity')->widget(NumberControl::classname(), [
                        'maskedInputOptions' => ['allowMinus' => false],
                        'options' => ['class' => 'form-control', 'placeholder' => 'Quantity'],
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'status_id')->widget(Select2::classname(), [
                        'data' => \common\modules\master\models\StatusActive::dropdown(),
                        'options' => [
                            'placeholder' => 'Status',
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6"></div>
            </div>

        <?php endif; ?>

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

<?php if ($isNew): $this->registerJs(<<<JS
(function () {
    var rowIndex = 1;

    function initProductSelect(\$select) {
        \$select.select2({
            width: '100%',
            theme: 'bootstrap4',
            placeholder: 'Product',
            allowClear: true,
            dropdownParent: \$select.closest('.modal').length ? \$select.closest('.modal') : \$(document.body)
        });
    }

    function updateRemoveButtons() {
        var \$rows = $('#bundle-item-rows .bundle-item-row');
        \$rows.find('.js-remove-row').prop('disabled', \$rows.length <= 1);
    }

    $('#bundle-item-rows .js-product-select').each(function () {
        initProductSelect($(this));
    });

    $('#js-add-bundle-row').on('click', function () {
        var template = $('#bundle-row-template').html().split('__INDEX__').join(rowIndex);
        var \$row = $(template);
        $('#bundle-item-rows').append(\$row);
        initProductSelect(\$row.find('.js-product-select'));
        rowIndex++;
        updateRemoveButtons();
    });

    $(document).on('click', '#bundle-item-rows .js-remove-row', function () {
        $(this).closest('.bundle-item-row').remove();
        updateRemoveButtons();
    });
})();
JS
); endif; ?>
