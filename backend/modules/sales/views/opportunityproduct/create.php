<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunityProduct $model */

$this->title = 'Create Opportunity Product';
$this->params['breadcrumbs'][] = ['label' => 'Opportunity Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="opportunity-product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
