<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunitySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<style>
    .kv-datepicker .input-group {
        flex-wrap: nowrap !important;
    }
</style>

<div class="opportunity-search">

    <?php 

    \kartik\date\DatePickerAsset::register($this);
    $form = ActiveForm::begin([
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
            'placeholder' => 'Name',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 150px;',
        ]) ?>

        <div style="width: 150px;">
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

        <?= $form->field($model, 'owner_user_id', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->textInput([
            'placeholder' => 'Owner User',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 150px;',
        ]) ?>

        <?php // echo $form->field($model, 'contact_id') ?>

        <div style="width: 150px;">
        <?= $form->field($model, 'stage', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->widget(Select2::classname(), [
            'data' => [ 
                'Prospecting' => 'Prospecting', 
                'Qualification' => 'Qualification', 
                'Proposal' => 'Proposal', 
                'Negotiation' => 'Negotiation', 
                'Closed Won' => 'Closed Won', 
                'Closed Lost' => 'Closed Lost', 
            ],
            'options' => [
                'placeholder' => 'Stage',
                'multiple' => false,
                'class' => 'form-control form-control-sm',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
            ],
        ])->label(false) ?>
        </div>

        <?php // echo $form->field($model, 'amount') ?>

        <?= $form->field($model, 'close_date', [
            'options' => ['class' => 'mb-0', 'style' => 'width:150px'],
            'template' => '{input}'
        ])->widget(DatePicker::classname(), [
            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
            'options' => [
                'placeholder' => 'Close Date',
            ],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'container' => 'body',
            ],
        ])->label(false); ?>

        <?php // echo $form->field($model, 'probability') ?>

        <?php // echo $form->field($model, 'description') ?>

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
<?php
$this->registerJs(<<<JS
$(document).ready(function() {
    $('#opportunitysearch-close_date').kvDatepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        container: 'body'
    });
});
JS);
?>