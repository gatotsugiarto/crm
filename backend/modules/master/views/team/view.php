<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\master\models\Team $model */


$this->title = 'Detail '.'Sales Teams';
$sub_title = 'Team management system';
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
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
                <span class="text-secondary small">Name</span><br>
                <span><small><?= Html::encode($model->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Description</span><br>
                <span><small><?= Html::encode($model->description) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Team Leader</span><br>
                <span><small><?= Html::encode($model->user?->fullname ?? '-') ?></small></span>
            </div>

        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <?php $members = $model->getUsers()->orderBy(['fullname' => SORT_ASC])->all(); ?>
                <span class="text-secondary small">Members (<?= count($members) ?>)</span><br>
                <?php if ($members): ?>
                    <?php foreach ($members as $member): ?>
                        <span class="badge badge-<?= $member->id == $model->user_id ? 'primary' : 'light' ?> mr-1 mb-1" style="font-size: 12px;">
                            <?= Html::encode($member->fullname) ?><?= $member->id == $model->user_id ? ' (leader)' : '' ?>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <small>-</small>
                <?php endif; ?>
                <br><small class="text-muted">Add or move members in User Management &rarr; Edit User Access &rarr; Sales Team.</small>
            </div>
        </div>


        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Created by:
                <strong><?= Html::encode($model->createdBy?->fullname ?? '-') ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->created_at) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="fa fa-edit"></i> Updated by:
                <strong><?= Html::encode($model->updatedBy?->fullname ?? '-') ?></strong>
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
