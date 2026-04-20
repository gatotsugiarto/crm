<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\EmployeePending $model */

$this->title = 'Create Employee Pending';
$this->params['breadcrumbs'][] = ['label' => 'Employee Pendings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-pending-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
