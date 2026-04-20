<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PayrollProfile $model */

$this->title = 'Update Payroll Profile: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Payroll Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payroll-profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
