<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\SalesOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sales-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>
    
    <div class="d-flex align-items-center" style="gap: 8px;">

        <?= $form->field($model, 'order_number', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->textInput([
            'placeholder' => 'Order Number',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 150px;',
        ]) ?>

        <div style="width: 200px;">
        <?= $form->field($model, 'account_id', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->widget(Select2::classname(), [
            'data' => \common\modules\sales\models\Account::dropdown(),
            'options' => [
                'placeholder' => 'Account',
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
        <?= $form->field($model, 'quotation_id', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->widget(Select2::classname(), [
            'data' => \common\modules\sales\models\Quotation::dropdown(),
            'options' => [
                'placeholder' => 'Quotation',
                'multiple' => false,
                'class' => 'form-control form-control-sm',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
            ],
        ])->label(false) ?>
        </div>


        <?= $form->field($model, 'order_date', [
            'options' => ['class' => 'mb-0', 'style' => 'width:150px'],
            'template' => '{input}'
        ])->widget(DatePicker::classname(), [
            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
            'options' => [
                'placeholder' => 'Order Date',
            ],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'container' => 'body',
            ],
        ])->label(false); ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'status_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

        <?=  Html::submitButton('<i class="fa fa-search"></i> Search', [
            'class' => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm',
        ]) ?>

        <?=  Html::a('<i class="fa fa-sync"></i> Reset', ['index'], [
            'class' => 'btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-sm',
        ]) ?>

    </div>
    <?php ActiveForm::end(); ?>

</div>
