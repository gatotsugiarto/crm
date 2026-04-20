<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PostalCode $model */

$this->title = 'Update Postal Code: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Postal Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="postal-code-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
