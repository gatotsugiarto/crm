<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductDiscount $model */

$this->title = 'Create Product Discount';
$this->params['breadcrumbs'][] = ['label' => 'Product Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-discount-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
