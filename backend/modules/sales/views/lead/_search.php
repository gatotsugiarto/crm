<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\LeadSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lead-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>
    
    <div class="d-flex align-items-center" style="gap: 8px;">

    <?= $form->field($model, 'company_name', [
        'options' => ['class' => 'mb-0'],
        'template' => '{input}'
    ])->textInput([
        'placeholder' => 'Company Name',
        'class' => 'form-control form-control-sm',
        'style' => 'width: 220px;',
    ]) ?>

    <?= $form->field($model, 'contact_name', [
        'options' => ['class' => 'mb-0'],
        'template' => '{input}'
    ])->textInput([
        'placeholder' => 'Contact Name',
        'class' => 'form-control form-control-sm',
        'style' => 'width: 220px;',
    ]) ?>

    <?= $form->field($model, 'email', [
        'options' => ['class' => 'mb-0'],
        'template' => '{input}'
    ])->textInput([
        'placeholder' => 'Email',
        'class' => 'form-control form-control-sm',
        'style' => 'width: 170px;',
    ]) ?>

    <?= $form->field($model, 'phone', [
        'options' => ['class' => 'mb-0'],
        'template' => '{input}'
    ])->textInput([
        'placeholder' => 'Phone',
        'class' => 'form-control form-control-sm',
        'style' => 'width: 120px;',
    ]) ?>

    <?php // echo $form->field($model, 'lead_source') ?>

    <?php // echo $form->field($model, 'industry') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'is_converted') ?>

    <?php // echo $form->field($model, 'converted_account_id') ?>

    <?php // echo $form->field($model, 'converted_contact_id') ?>

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
