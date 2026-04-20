<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\SalesOrderItem $model */

$this->title = 'Create Sales Order Item';
$this->params['breadcrumbs'][] = ['label' => 'Sales Order Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-order-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
