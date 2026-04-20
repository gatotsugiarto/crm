<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\master\models\ApplicationSetting $model */

$this->title = 'Create Application Setting';
$this->params['breadcrumbs'][] = ['label' => 'Application Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
