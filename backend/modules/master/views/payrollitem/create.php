<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PayrollItem $model */

$this->title = 'Create Payroll Item';
$this->params['breadcrumbs'][] = ['label' => 'Payroll Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payroll-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
