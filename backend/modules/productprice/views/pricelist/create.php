<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PriceList $model */

$this->title = 'Create Price List';
$this->params['breadcrumbs'][] = ['label' => 'Price Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
