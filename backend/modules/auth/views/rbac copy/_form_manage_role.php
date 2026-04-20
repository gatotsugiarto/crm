<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64, 'style'=>'width:300px']) ?>
    
    <?= $form->field($model, 'type')->HiddenInput(['value' => 1])->label(false) ?>
    
    <?= $form->field($model, 'description')->textarea(['style'=>'width:550px', 'rows' => '6']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create Role' : 'Update Role', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
