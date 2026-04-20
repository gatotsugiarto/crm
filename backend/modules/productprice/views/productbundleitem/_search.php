<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductBundleItemSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-bundle-item-search">
<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => ['data-pjax' => 1],
]); ?>
    <div class="d-flex align-items-center" style="gap: 8px;">

        <div style="width: 220px;">
            <?= $form->field($model, 'bundle_product_id', [
                'options' => ['class' => 'mb-0'],
                'template' => '{input}'
            ])->widget(Select2::class, [
                'data' => \common\modules\productprice\models\Product::dropdown(),
                'options' => [
                    'placeholder' => 'Bundle Product',
                    'class' => 'form-control form-control-sm',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                ],
            ]) ?>
        </div>

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

        <?=  Html::submitButton('<i class="fa fa-search"></i> Search', [
            'class' => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm',
        ]) ?>

        <?=  Html::a('<i class="fa fa-sync"></i> Reset', ['index'], [
            'class' => 'btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-sm',
        ]) ?>

    </div>
    <?php ActiveForm::end(); ?>

</div>
