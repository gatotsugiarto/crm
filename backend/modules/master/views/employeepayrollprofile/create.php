<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\EmployeePayrollProfile $model */

$this->title = 'Create Employee Payroll Profile';
$this->params['breadcrumbs'][] = ['label' => 'Employee Payroll Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-payroll-profile-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
