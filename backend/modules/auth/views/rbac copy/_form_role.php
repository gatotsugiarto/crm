<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'style'=>'width:300px']) ?>

    <?= $form->field($model, 'type')->HiddenInput(['value' => 1])->label(false) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'style'=>'width:500px']) ?>

    <?= $form->field($model, 'data')->dropDownList(
            [1 => 'No', 2 => 'Yes'],
            ['style'=>'width:240px']
    	)->label('Showing At Registration?'); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '<span class=\'glyphicon glyphicon-check\'></span> Save' : '<span class=\'glyphicon glyphicon-edit\'></span> Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'style' => 'width:150px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
