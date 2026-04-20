<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\master\models\EmployeePendingSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="employee-pending-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'employee_id') ?>

    <?= $form->field($model, 'e_number') ?>

    <?= $form->field($model, 'marital_status_id') ?>

    <?= $form->field($model, 'marital_status') ?>

    <?php // echo $form->field($model, 'family_status_id') ?>

    <?php // echo $form->field($model, 'family_status') ?>

    <?php // echo $form->field($model, 'ptkp_id') ?>

    <?php // echo $form->field($model, 'ptkp') ?>

    <?php // echo $form->field($model, 'pending_status') ?>

    <?php // echo $form->field($model, 'status_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
