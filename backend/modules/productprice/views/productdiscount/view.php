<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\ProductDiscount $model */


$this->title = 'Detail '.'Product Discounts';
$sub_title = 'Set and control product discounts';
$this->params['breadcrumbs'][] = ['label' => 'Product Discounts', 'url' => ['index']];
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
                <span class="text-secondary small">Product</span><br>
                <span><small><?= Html::encode($model->product?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Price list</span><br>
                <span><small><?= Html::encode($model->priceList?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Discount type</span><br>
                <span><small><?= Html::encode($model->discount_type) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Discount value</span><br>
                <span><small><?= Html::encode($model->discount_value) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Priority</span><br>
                <span><small><?= Html::encode($model->priority) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Min qty</span><br>
                <span><small><?= Html::encode($model->min_qty) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Valid from</span><br>
                <span><small><?= Html::encode($model->valid_from) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Valid to</span><br>
                <span><small><?= Html::encode($model->valid_to) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Stackable</span><br>
                <span>
                    <?php if ($model->is_stackable): ?>
                        <span class="badge bg-success">Stackable</span>
                        <small class="text-muted d-block">Can be combined with other discounts</small>
                    <?php else: ?>
                        <span class="badge bg-secondary">Not Stackable</span>
                        <small class="text-muted d-block">Cannot be combined with other discounts</small>
                    <?php endif; ?>
                </span>
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
