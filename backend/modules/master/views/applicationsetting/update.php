<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\ApplicationSetting $model */

$this->title = 'Update Application Setting: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Application Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="application-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
