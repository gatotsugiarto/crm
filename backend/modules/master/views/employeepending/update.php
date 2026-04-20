<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\EmployeePending $model */

$this->title = 'Update Employee Pending: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Employee Pendings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="employee-pending-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
