<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunityStageHistory $model */

$this->title = 'Create Opportunity Stage History';
$this->params['breadcrumbs'][] = ['label' => 'Opportunity Stage Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="opportunity-stage-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
