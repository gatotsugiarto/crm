<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunityStageHistory $model */


$this->title = 'Detail '.'Opportunity Stage Histories';
$sub_title = 'Detailed timeline of this opportunity’s stage changes';
$this->params['breadcrumbs'][] = ['label' => 'Opportunity Stage Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?= $this->title ?>
    </h5>
    <p class="text-muted small mb-0">
        <?=$sub_title ?>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Opportunity</span><br>
                <span><small><?= Html::encode($model->opportunity->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Old stage</span><br>
                <span><small><?= Html::encode($model->old_stage) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">New stage</span><br>
                <span><small><?= Html::encode($model->new_stage) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Changed at</span><br>
                <span><small><?= Html::encode($model->changed_at) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Days in previous stage</span><br>
                <span><small><?= Html::encode($model->days_in_previous_stage) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Description</span><br>
                <span><small><?= Html::encode($model->description) ?></small></span>
            </div>
        </div>

        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Change by:
                <strong><?= Html::encode($model->changedBy->fullname) ?></strong>
                <br>
                <span class="text-secondary small">Change At</span> <i class="far fa-clock"></i> <small><?= Html::encode($model->changed_at) ?></small>
            </div>
        </div>

    </div>
</div>

<div class="text-end mt-3">
<?php if (Yii::$app->request->isAjax): ?>

    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>

<?php else: ?>

    <?= Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
        'class' => 'btn btn-outline-secondary',
        'style' => 'min-width:140px;',
    ]) ?>

<?php endif; ?>
</div>
