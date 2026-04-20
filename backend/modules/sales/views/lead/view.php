<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Lead $model */


$this->title = 'Detail '.'Leads';
$sub_title = 'Manage and track potential customers';
$this->params['breadcrumbs'][] = ['label' => 'Leads', 'url' => ['index']];
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
                <span class="text-secondary small">Company name</span><br>
                <span><small><?= Html::encode($model->company_name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Contact name</span><br>
                <span><small><?= Html::encode($model->contact_name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Email</span><br>
                <span><small><?= Html::encode($model->email) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Phone</span><br>
                <span><small><?= Html::encode($model->phone) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Lead source</span><br>
                <span><small><?= Html::encode($model->lead_source) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Industry</span><br>
                <span><small><?= Html::encode($model->industry) ?></small></span>
            </div>
        </div>

        <hr class="my-2">

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Address</span><br>
                <span><small><?= Html::encode($model->address) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">City</span><br>
                <span><small><?= Html::encode($model->city->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Province</span><br>
                <span><small><?= Html::encode($model->province->name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Country</span><br>
                <span><small><?= Html::encode($model->country->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Postal Code</span><br>
                <span><small><?= Html::encode($model->postalCode->code) ?></small></span>
            </div>
        </div>

        <hr class="my-2">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <?= $model->is_converted
                    ? Html::tag('span', '<i class="fa fa-check-circle"></i> Converted', [
                        'class'          => 'badge badge-success px-2 py-1',
                        'title'          => $model->convertedAccount->name ?? 'Account',
                        'data-toggle'    => 'tooltip',
                        'data-placement' => 'top',
                    ])
                    : Html::tag('span', '<i class="fa fa-clock"></i> New', [
                        'class' => 'badge badge-warning px-2 py-1',
                    ])
                ?>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Owner User</span><br>
                <span><small><?= Html::encode($model->team?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Converted Account</span><br>
                <span><small><?= Html::encode($model->account?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Converted Contact</span><br>
                <span><small><?= Html::encode($model->contact?->fullname ?? '-') ?></small></span>
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
