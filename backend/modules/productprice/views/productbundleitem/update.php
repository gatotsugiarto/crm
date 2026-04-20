<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductBundleItem $model */

$this->title = 'Update Product Bundle Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Product Bundle Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-bundle-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
