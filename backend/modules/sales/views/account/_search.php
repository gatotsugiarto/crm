<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\AccountSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>
    
    <div class="d-flex align-items-center" style="gap: 8px;">

        <?= $form->field($model, 'name', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->textInput([
            'placeholder' => 'Account Name',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 220px;',
        ]) ?>

        <?= $form->field($model, 'phone', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->textInput([
            'placeholder' => 'Phone',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 120px;',
        ]) ?>

        <div style="width: 200px;">
        <?= $form->field($model, 'price_list_id', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->widget(Select2::classname(), [
            'data' => \common\modules\productprice\models\PriceList::dropdown(),
            'options' => [
                'placeholder' => 'Price List',
                'multiple' => false,
                'class' => 'form-control form-control-sm',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
            ],
        ])->label(false) ?>
        </div>

        <div style="width: 200px;">
        <?= $form->field($model, 'account_type', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->widget(Select2::classname(), [
            'data' => [
                'Prospect' => 'Prospect',
                'Customer' => 'Customer',
                'Partner' => 'Partner',
                'Reseller' => 'Reseller',
                'Vendor' => 'Vendor',
            ],
            'options' => [
                'placeholder' => 'Type',
                'multiple' => false,
                'class' => 'form-control form-control-sm',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
            ],
        ])->label(false) ?>
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
