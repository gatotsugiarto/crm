<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunityProduct $model */


$this->title = 'Detail '.'Opportunity Products';
$sub_title = 'Detailed view of products within this opportunity';
$this->params['breadcrumbs'][] = ['label' => 'Opportunity Products', 'url' => ['index']];
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
                <span class="text-secondary small">Product</span><br>
                <span><small><?= Html::encode($model->product->name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Qty</span><br>
                <span><small><?= Html::encode($model->qty) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Price</span><br>
                <span><small><?= Yii::$app->formatter->asCurrency($model->price) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Discount</span><br>
                <span><small><?= Html::encode($model->discount) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Total</span><br>
                <span><small><?= Yii::$app->formatter->asCurrency($model->total) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Is Upsell</span><br>
                <span><small><?= $model->is_upsell == 1
                        ? Html::tag('span', 'Yes', ['class' => 'badge badge-success'])
                        : Html::tag('span', 'No', ['class' => 'badge badge-secondary'])
                    ?></small>
                </span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Parent Product</span><br>
                <span><small><?= Html::encode($model->parentProduct?->product->name ?? '-') ?></small></span>
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
