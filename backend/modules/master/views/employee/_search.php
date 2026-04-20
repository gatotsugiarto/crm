<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\master\models\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'region') ?>

    <?= $form->field($model, 'company_id') ?>

    <?= $form->field($model, 'company') ?>

    <?php // echo $form->field($model, 'branch_id') ?>

    <?php // echo $form->field($model, 'branch') ?>

    <?php // echo $form->field($model, 'site_office_id') ?>

    <?php // echo $form->field($model, 'site_office') ?>

    <?php // echo $form->field($model, 'department_id') ?>

    <?php // echo $form->field($model, 'department') ?>

    <?php // echo $form->field($model, 'division_id') ?>

    <?php // echo $form->field($model, 'division') ?>

    <?php // echo $form->field($model, 'e_number') ?>

    <?php // echo $form->field($model, 'fullname') ?>

    <?php // echo $form->field($model, 'join_date') ?>

    <?php // echo $form->field($model, 'marital_status_id') ?>

    <?php // echo $form->field($model, 'marital_status') ?>

    <?php // echo $form->field($model, 'family_status_id') ?>

    <?php // echo $form->field($model, 'family_status') ?>

    <?php // echo $form->field($model, 'level_jabatan_id') ?>

    <?php // echo $form->field($model, 'level_jabatan') ?>

    <?php // echo $form->field($model, 'jabatan_id') ?>

    <?php // echo $form->field($model, 'jabatan') ?>

    <?php // echo $form->field($model, 'grade_id') ?>

    <?php // echo $form->field($model, 'grade') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'is_npwp') ?>

    <?php // echo $form->field($model, 'npwp_id') ?>

    <?php // echo $form->field($model, 'bpjs_tk') ?>

    <?php // echo $form->field($model, 'bpjs_kes') ?>

    <?php // echo $form->field($model, 'bank_id') ?>

    <?php // echo $form->field($model, 'bank') ?>

    <?php // echo $form->field($model, 'bank_no') ?>

    <?php // echo $form->field($model, 'employee_status_id') ?>

    <?php // echo $form->field($model, 'employee_status') ?>

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
