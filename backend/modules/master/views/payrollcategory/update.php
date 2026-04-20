<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PayrollCategory $model */

$this->title = 'Update Payroll Category: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Payroll Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payroll-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
