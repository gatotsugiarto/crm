<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\master\models\Product $model */


$this->title = 'Detail '.'Products';
$sub_title = 'Detailed information about this product';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
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
                <span class="text-secondary small">Code</span><br>
                <span><small><?= Html::encode($model->code) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Product</span><br>
                <span><small><?= Html::encode($model->name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Category</span><br>
                <span><small><?= Html::encode($model->category->name) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Uom</span><br>
                <span><small><?= Html::encode($model->uom->name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Type</span><br>
                <span><small><?= Html::encode($model->type) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Bundle price type</span><br>
                <span><small><?= Html::encode($model->bundle_price_type) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Description</span><br>
                <span><small><?= Html::encode($model->description) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Base price (COGS)</span><br>
                <span><small><?= Yii::$app->formatter->asCurrency($model->base_price, 'IDR') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Bundle Expand</span><br>
                <span>
                    <?php if ($model->is_bundle_expand): ?>
                        <span class="badge bg-success">Expandable</span>
                        <small class="text-muted d-block">Can add more products to this bundle</small>
                    <?php else: ?>
                        <span class="badge bg-secondary">Fixed</span>
                        <small class="text-muted d-block">Bundle cannot be expanded</small>
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
