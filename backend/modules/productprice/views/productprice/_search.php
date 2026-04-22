<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
?>
<div class="product-price-search mb-2">
<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => ['data-pjax' => 1],
]); ?>
    <div class="d-flex align-items-center" style="gap: 8px;">

        <?= Html::hiddenInput('ProductPriceSearch[price_list_id]', $model->price_list_id) ?>

        <div style="width: 220px;">
            <?= $form->field($model, 'product_id', [
                'options' => ['class' => 'mb-0'],
                'template' => '{input}'
            ])->widget(Select2::class, [
                'data' => \common\modules\productprice\models\Product::dropdown(),
                'options' => [
                    'placeholder' => 'All Product',
                    'class' => 'form-control form-control-sm',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                ],
            ]) ?>
        </div>

        <div style="width: 220px;">
            <?= $form->field($model, 'price_list_id', [
                'options' => ['class' => 'mb-0'],
                'template' => '{input}'
            ])->widget(Select2::class, [
                'data' => \common\modules\productprice\models\PriceList::dropdown(),
                'options' => [
                    'placeholder' => 'Select Price List',
                    'class' => 'form-control form-control-sm',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                ],
            ]) ?>
        </div>

        <?= $form->field($model, 'valid_from', [
            'options'  => ['class' => 'mb-0'],
            'template' => '{input}',
        ])->textInput([
            'type'        => 'date',
            'class'       => 'form-control form-control-sm',
            'placeholder' => 'Valid From',
            'title'       => 'Valid From (on or after)',
        ]) ?>

        <?= $form->field($model, 'valid_to', [
            'options'  => ['class' => 'mb-0'],
            'template' => '{input}',
        ])->textInput([
            'type'        => 'date',
            'class'       => 'form-control form-control-sm',
            'placeholder' => 'Valid To',
            'title'       => 'Valid To (on or before)',
        ]) ?>

        <?= Html::submitButton('<i class="fa fa-search"></i> Search', [
            'class' => 'btn btn-primary btn-sm px-3',
        ]) ?>

        <?= Html::a('<i class="fa fa-sync"></i> Reset', ['view', 'id' => $model->price_list_id], [
            'class' => 'btn btn-outline-secondary btn-sm px-3',
        ]) ?>

    </div>
<?php ActiveForm::end(); ?>
</div>