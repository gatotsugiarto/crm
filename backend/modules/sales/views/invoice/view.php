<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Invoice $model */

$this->title = 'Detail Invoices';
$sub_title = 'Review invoice details and payment status';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <?php
        $statusClass = match($model->status) {
            'Paid'    => 'success',
            'Sent'    => 'info',
            'Overdue' => 'danger',
            default   => 'secondary'
        };
        ?>
        <span class="badge badge-<?= $statusClass ?> px-3 py-2">
            <?= Html::encode($model->status) ?>
        </span>
    </div>

    <div class="d-flex gap-2">

        <?php if ($model->status === 'Draft'): ?>
            <?= Html::a('<i class="fa fa-paper-plane"></i> Mark as Sent',
                ['mark-sent', 'id' => $model->id], [
                'class'        => 'btn btn-info btn-sm',
                'data-confirm' => 'Mark this invoice as Sent?',
                'data-method'  => 'post',
            ]) ?>
        <?php endif; ?>

        <?php if ($model->status === 'Sent'): ?>
            <?= Html::a('<i class="fa fa-check-circle"></i> Mark as Paid',
                ['mark-paid', 'id' => $model->id], [
                'class'        => 'btn btn-success btn-sm',
                'data-confirm' => 'Mark this invoice as Paid?',
                'data-method'  => 'post',
            ]) ?>
        <?php endif; ?>

        <?php if ($model->salesOrder): ?>
            <?= Html::a('<i class="fa fa-shopping-cart"></i> View Sales Order',
                ['/sales/salesorder/view', 'id' => $model->sales_order_id], [
                'class' => 'btn btn-outline-primary btn-sm',
            ]) ?>
        <?php endif; ?>

        <?= Html::a('<i class="fa fa-file-pdf"></i> Invoice PDF',
            ['pdf', 'id' => $model->id], [
            'class'  => 'btn btn-outline-danger btn-sm',
            'target' => '_blank',
        ]) ?>

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
                <span class="text-secondary small">Invoice number</span><br>
                <span><small><?= Html::encode($model->invoice_number) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Account</span><br>
                <span><small><?= Html::encode($model->account?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Sales order</span><br>
                <span><small><?= Html::encode($model->salesOrder?->order_number ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Invoice date</span><br>
                <span><small><?= Html::encode($model->invoice_date) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Due date</span><br>
                <span><small><?= Html::encode($model->due_date) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Total amount</span><br>
                <span><small><?= Yii::$app->formatter->asCurrency($model->total_amount) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <?php
                $statusClass = match($model->status) {
                    'Paid'    => 'success',
                    'Sent'    => 'info',
                    'Overdue' => 'danger',
                    default   => 'secondary'
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
     INVOICE ITEM SECTION
====================================================== -->
<?php Pjax::begin(['id' => 'pp-pjax']); ?>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-tags"></i>&nbsp; Invoice Items
            </div>
            <small class="text-muted">Track all line items within this invoice</small>
        </div>
        <?php if ($model->status === 'Draft'): ?>
            <?= Html::button('<i class="fa fa-plus"></i> Add Invoice Item', [
                'class'    => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-invoiceitem',
                'data-url' => Url::to(['/sales/invoiceitem/create', 'invoice_id' => $model->id]),
            ]) ?>
        <?php endif; ?>
    </div>
    <?= $this->render('@backend/modules/sales/views/invoiceitem/_search', [
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
            'attribute' => 'invoice_id',
            'format'    => 'raw',
            'value'     => fn($m) => Html::a(
                Html::encode($m->invoice->invoice_number),
                'javascript:void(0);',
                [
                    'class'    => 'text-primary view-data',
                    'data-url' => Url::to(['/sales/invoiceitem/view', 'id' => $m->id]),
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
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-invoiceitem',
                    'data-url' => Url::to(['/sales/invoiceitem/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-invoiceitem-js',
                    'data-url'  => Url::to(['/sales/invoiceitem/delete', 'id' => $m->id]),
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
     MODAL: CREATE / EDIT INVOICE ITEM
======================================================== -->
<div class="modal fade" id="invoiceitemModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL: DELETE INVOICE ITEM
======================================================== -->
<div class="modal fade" id="deleteinvoiceitemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-invoiceitem-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class'        => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style'        => 'min-width:140px;',
                ]) ?>
                <form id="delete-invoiceitem-form" method="post">
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
        'class'        => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style'        => 'min-width:140px;',
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

/* CREATE / EDIT INVOICE ITEM */
$(document).on('click', '.create-invoiceitem, .edit-invoiceitem', function () {
    $('#invoiceitemModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#invoiceitemModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#invoiceitem-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#invoiceitemModal').modal('hide');
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

/* DELETE INVOICE ITEM */
$(document).on('click', '.delete-invoiceitem-js', function () {
    $('#delete-invoiceitem-name').text($(this).data('name'));
    $('#delete-invoiceitem-form').attr('action', $(this).data('url'));
    $('#deleteinvoiceitemModal').modal('show');
});

$(document).on('submit', '#delete-invoiceitem-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deleteinvoiceitemModal').modal('hide');
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
