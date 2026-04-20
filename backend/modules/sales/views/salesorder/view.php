<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\SalesOrder $model */


$this->title = 'Detail '.'Sales Orders';
$sub_title = 'Detailed information about this sales order';
$this->params['breadcrumbs'][] = ['label' => 'Sales Orders', 'url' => ['index']];
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
                <span class="text-secondary small">Order number</span><br>
                <span><small><?= Html::encode($model->order_number) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Account</span><br>
                <span><small><?= Html::encode($model->account?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Quotation</span><br>
                <span><small><?= Html::encode($model->quotation?->quotation_number ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Order date</span><br>
                <span><small><?= Html::encode($model->order_date) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Total amount</span><br>
                <span><small><?= Yii::$app->formatter->asCurrency($model->total_amount) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <span><small><?php if ($model->status === 'Draft'): ?>
                                <?= Html::a('<i class="fa fa-check"></i> Confirm SO', ['confirm', 'id' => $model->id], [
                                    'class' => 'btn btn-success',
                                    'data-confirm' => 'Confirm this Sales Order?',
                                    'data-method' => 'post',
                                ]) ?>
                            <?php else: ?>
                                <?= Html::encode($model->status) ?>
                            <?php endif; ?>
                </small></span>
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

<!-- =====================================================
     SALES ORDER ITEM SECTION
====================================================== -->
<?php Pjax::begin(['id' => 'pp-pjax']); ?>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-tags"></i>&nbsp; Sales Order Items
            </div>
            <small class="text-muted">List of products and services within orders</small>
        </div>
        <?php if ($model->status !== 'Approved'): ?>
            <?= Html::button('<i class="fa fa-plus"></i> Add SO Item', [
                'class'    => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-quotation-item',
                'data-url' => Url::to(['/sales/salesorderitem/create', 'opportunity_id' => $model->id]),
            ]) ?>
        <?php endif; ?>
    </div>
    <?= $this->render('@backend/modules/sales/views/salesorderitem/_search', [
        'model' => $searchModel,
    ]) ?>
</div>

<div style="overflow-x:auto; width:100%;">
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'hover'            => true,
    'resizableColumns' => false,
    'export'           => false,
    'tableOptions'     => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout'           => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn', 'header' => 'No'],
        [
            'attribute' => 'sales_order_id',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->salesOrder->order_number),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['/sales/salesorderitem/view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute' => 'product_id',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->product->name;
            },
        ],
        [
            'attribute'      => 'qty',
            'label'          => 'Qty',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'price',
            'label'          => 'Price',
            'format'         => 'raw',
            'value'          => fn($m) => Yii::$app->formatter->asCurrency($m->price),
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions'  => ['class' => 'text-white text-right'],
        ],
        [
            'attribute'      => 'discount',
            'label'          => 'Discount',
            'format'         => 'raw',
            'value'          => fn($m) => Yii::$app->formatter->asCurrency($m->discount),
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions'  => ['class' => 'text-white text-right'],
        ],
        [
            'attribute'      => 'total',
            'label'          => 'Total',
            'format'         => 'raw',
            'value'          => fn($m) => Yii::$app->formatter->asCurrency($m->total),
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions'  => ['class' => 'text-white text-right'],
        ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Action',
            'template'       => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'visibleButtons' => [
                'update' => fn($m) => $model->status !== 'Approved',
                'delete' => fn($m) => $model->status !== 'Approved',
            ],
            'buttons'        => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-quotation-item',
                    'data-url' => Url::to(['/sales/quotationitem/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-quotation-item-js',
                    'data-url'  => Url::to(['/sales/quotationitem/delete', 'id' => $m->id]),
                    'data-name' => ($m->opportunity->name ?? '-'),
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

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
