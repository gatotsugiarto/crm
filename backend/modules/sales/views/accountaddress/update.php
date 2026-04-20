<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\AccountAddress $model */

$this->title = 'Update Account Address: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Account Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="account-address-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
