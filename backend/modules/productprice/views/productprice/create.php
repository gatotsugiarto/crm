<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\ProductPrice $model */

$this->title = 'Create Product Price';
$this->params['breadcrumbs'][] = ['label' => 'Product Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-price-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
