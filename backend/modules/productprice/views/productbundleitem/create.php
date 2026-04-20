<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductBundleItem $model */

$this->title = 'Create Product Bundle Item';
$this->params['breadcrumbs'][] = ['label' => 'Product Bundle Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-bundle-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
