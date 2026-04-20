<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\master\models\Employee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_id')->textInput() ?>

    <?= $form->field($model, 'region')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_id')->textInput() ?>

    <?= $form->field($model, 'company')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'branch')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'site_office_id')->textInput() ?>

    <?= $form->field($model, 'site_office')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_id')->textInput() ?>

    <?= $form->field($model, 'department')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'division_id')->textInput() ?>

    <?= $form->field($model, 'division')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'join_date')->textInput() ?>

    <?= $form->field($model, 'marital_status_id')->textInput() ?>

    <?= $form->field($model, 'marital_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'family_status_id')->textInput() ?>

    <?= $form->field($model, 'family_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'level_jabatan_id')->textInput() ?>

    <?= $form->field($model, 'level_jabatan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jabatan_id')->textInput() ?>

    <?= $form->field($model, 'jabatan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grade_id')->textInput() ?>

    <?= $form->field($model, 'grade')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_npwp')->textInput() ?>

    <?= $form->field($model, 'npwp_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bpjs_tk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'bpjs_kes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'bank_id')->textInput() ?>

    <?= $form->field($model, 'bank')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'employee_status_id')->textInput() ?>

    <?= $form->field($model, 'employee_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
