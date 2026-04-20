<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PayrollItemSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payroll-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'category_id') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'sign') ?>

    <?php // echo $form->field($model, 'affects_gross_tax') ?>

    <?php // echo $form->field($model, 'taxable') ?>

    <?php // echo $form->field($model, 'display_order') ?>

    <?php // echo $form->field($model, 'percent') ?>

    <?php // echo $form->field($model, 'cap') ?>

    <?php // echo $form->field($model, 'salary_type') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
