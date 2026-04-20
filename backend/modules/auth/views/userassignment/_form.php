<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\UserAssignment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-assignment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $options = common\modules\auth\models\User::dropdown();
    echo $form->field($model, 'user_id')->dropDownList($options, [
        'prompt' => 'Select user...', 
    ]) ?>

    <?= $form->field($model, 'item_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
