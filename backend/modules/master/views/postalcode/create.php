<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PostalCode $model */

$this->title = 'Create Postal Code';
$this->params['breadcrumbs'][] = ['label' => 'Postal Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="postal-code-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
