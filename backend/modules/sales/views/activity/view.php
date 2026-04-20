<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Activity $model */


$this->title = 'Detail '.'Activities';
$sub_title = 'Detailed information about this activity';
$this->params['breadcrumbs'][] = ['label' => 'Activities', 'url' => ['index']];
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
                <span class="text-secondary small">Account</span><br>
                <span><small><?= Html::encode($model->account->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Contact</span><br>
                <span><small><?= Html::encode($model->contact?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Opportunity</span><br>
                <span><small><?= Html::encode($model->opportunity->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Reference type</span><br>
                <span><small><?= Html::encode($model->reference_type) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Reference</span><br>
                <span><small><?= Html::encode($model->reference_id) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Assigned to</span><br>
                <span><small><?= Html::encode($model->assigned_to) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Activity type</span><br>
                <span><small><?= Html::encode($model->activity_type) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Priority</span><br>
                <span><small><?= Html::encode($model->priority) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Subject</span><br>
                <span><small><?= Html::encode($model->subject) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Activity date</span><br>
                <span><small><?= Html::encode($model->activity_date) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Due date</span><br>
                <span><small><?= Html::encode($model->due_date) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Reminder at</span><br>
                <span><small><?= Html::encode($model->reminder_at) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Is completed</span><br>
                <span><small><?= Html::encode($model->is_completed) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Completed at</span><br>
                <span><small><?= Html::encode($model->completed_at) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Description</span><br>
                <span><small><?= Html::encode($model->description) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Outcome</span><br>
                <span><small><?= Html::encode($model->outcome) ?></small></span>
            </div>
        </div>


        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Created by:
                <strong><?= Html::encode($model->createdBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->created_at) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="fa fa-edit"></i> Updated by:
                <strong><?= Html::encode($model->updatedBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?=Html::encode($model->updated_at) ?></small>
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
