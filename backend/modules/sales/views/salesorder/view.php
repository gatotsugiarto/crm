<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\SalesOrder $model */

$this->title = 'Detail Sales Orders';
$sub_title = 'Detailed information about this sales order';
$this->params['breadcrumbs'][] = ['label' => 'Sales Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$invoice = $model->invoices[0] ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <?php
        $statusClass = match($model->status) {
            'Confirmed' => 'info',
            'Completed' => 'success',
            'Cancelled' => 'danger',
            default     => 'secondary'
        };
        ?>
        <span class="badge badge-<?= $statusClass ?> px-3 py-2">
            <?= Html::encode($model->status) ?>
        </span>
    </div>

    <div class="d-flex gap-2">

        <?php if ($model->status === 'Draft'): ?>
            <?= Html::a('<i class="fa fa-check"></i> Confirm SO',
                ['confirm', 'id' => $model->id], [
                'class'        => 'btn btn-success btn-sm',
                'data-confirm' => 'Confirm this Sales Order?',
                'data-method'  => 'post',
            ]) ?>
        <?php endif; ?>

        <?php if ($invoice): ?>
            <?= Html::a('<i class="fa fa-file-invoice"></i> View Invoice',
                ['/sales/invoice/view', 'id' => $invoice->id], [
                'class' => 'btn btn-primary btn-sm',
            ]) ?>
        <?php endif; ?>

    </div>

</div>

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
                <?php
                $statusClass = match($model->status) {
                    'Confirmed' => 'info',
                    'Completed' => 'success',
                    'Cancelled' => 'danger',
                    default     => 'secondary'
                };
                ?>
                <span class="text-secondary small">Status</span><br>
                <span class="badge badge-<?= $statusClass ?>">
                    <?= Html::encode($model->status) ?>
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
            <small class="text-muted">List of products and services within this order</small>
        </div>
        <?php if ($model->status === 'Draft'): ?>
            <?= Html::button('<i class="fa fa-plus"></i> Add SO Item', [
                'class'    => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-salesorderitem',
                'data-url' => Url::to(['/sales/salesorderitem/create', 'sales_order_id' => $model->id]),
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
            'format'    => 'raw',
            'value'     => fn($m) => Html::a(
                Html::encode($m->salesOrder->order_number),
                'javascript:void(0);',
                [
                    'class'    => 'text-primary view-data',
                    'data-url' => Url::to(['/sales/salesorderitem/view', 'id' => $m->id]),
                ]
            ),
        ],
        [
            'attribute' => 'product_id',
            'format'    => 'raw',
            'value'     => fn($m) => $m->product->name,
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
                'update' => fn($m) => $model->status === 'Draft',
                'delete' => fn($m) => $model->status === 'Draft',
            ],
            'buttons' => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-salesorderitem',
                    'data-url' => Url::to(['/sales/salesorderitem/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-salesorderitem-js',
                    'data-url'  => Url::to(['/sales/salesorderitem/delete', 'id' => $m->id]),
                    'data-name' => ($m->product->name ?? '-'),
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- =======================================================
     MODAL: VIEW DETAIL
======================================================== -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL: CREATE / EDIT SO ITEM
======================================================== -->
<div class="modal fade" id="salesorderitemModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL: DELETE SO ITEM
======================================================== -->
<div class="modal fade" id="deletesalesorderitemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-salesorderitem-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class'        => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style'        => 'min-width:140px;',
                ]) ?>
                <form id="delete-salesorderitem-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Delete', [
                        'class' => 'btn btn-danger px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
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

<?php
$this->registerJs(<<<JS

/* VIEW ITEM DETAIL */
$(document).on('click', '.view-data', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});

/* CREATE / EDIT SO ITEM */
$(document).on('click', '.create-salesorderitem, .edit-salesorderitem', function () {
    $('#salesorderitemModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#salesorderitemModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#salesorderitem-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#salesorderitemModal').modal('hide');
            $.pjax.reload({container: '#pp-pjax', timeout: 500}).done(function () {
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            });
        } else if (res && res.errors) {
            form.yiiActiveForm('updateMessages', res.errors, true);
        }
    }, 'json');
    return false;
});

/* DELETE SO ITEM */
$(document).on('click', '.delete-salesorderitem-js', function () {
    $('#delete-salesorderitem-name').text($(this).data('name'));
    $('#delete-salesorderitem-form').attr('action', $(this).data('url'));
    $('#deletesalesorderitemModal').modal('show');
});

$(document).on('submit', '#delete-salesorderitem-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deletesalesorderitemModal').modal('hide');
        $.pjax.reload({container: '#pp-pjax', timeout: 500}).done(function () {
            if (res && res.success) {
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            }
        });
    }, 'json');
});

JS);
?>
