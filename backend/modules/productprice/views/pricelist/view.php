<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var common\modules\master\models\PriceList $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\modules\productprice\models\ProductPriceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $discountProvider */
/** @var common\modules\productprice\models\ProductDiscountSearch $discountSearchModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Price Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="alert-container"></div>

<!-- HEADER -->
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <div class="text-primary fw-bold page-title">
                <i class="fa fa-list-alt"></i>&nbsp;&nbsp;
                <?= Html::encode($model->name) ?>
            </div>
            <small class="text-muted">
                Manage product prices for this price list
            </small>
        </div>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Back', ['index'], [
            'class' => 'btn btn-sm btn-outline-secondary px-3 rounded-pill',
        ]) ?>
    </div>
</div>

<!-- DETAIL CARD -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Name</span><br>
                <span><small><?= Html::encode($model->name) ?></small></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Currency</span><br>
                <span><small><?= Html::encode($model->currency) ?></small></span>
            </div>
        </div>
        <hr class="my-2">
        <div class="row small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Created by:
                <strong><?= Html::encode($model->createdBy->fullname) ?></strong><br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->created_at) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="fa fa-edit"></i> Updated by:
                <strong><?= Html::encode($model->updatedBy->fullname) ?></strong><br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->updated_at) ?></small>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================
     PRODUCT PRICE SECTION
====================================================== -->
<?php Pjax::begin(['id' => 'pp-pjax']); ?>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-tags"></i>&nbsp; Product Prices
            </div>
            <small class="text-muted">Product pricing details for this price list</small>
        </div>
        <?= Html::button('<i class="fa fa-plus"></i> Add Product Price', [
            'class'    => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-price',
            'data-url' => Url::to(['/productprice/productprice/create', 'price_list_id' => $model->id]),
        ]) ?>
    </div>
    <?= $this->render('@backend/modules/productprice/views/productprice/_search', [
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
            'attribute' => 'product_id',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->product?->name ?? ''),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['/productprice/productprice/view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute'      => 'min_qty',
            'label'          => 'Min Qty',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'price',
            'label'          => 'Price',
            'format'         => 'raw',
            'value'          => fn($m) => Yii::$app->formatter->asDecimal($m->price, 2),
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions'  => ['class' => 'text-white text-right'],
        ],
        [
            'attribute'      => 'valid_from',
            'label'          => 'Valid From',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'valid_to',
            'label'          => 'Valid To',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'status_id',
            'format'         => 'raw',
            'label'          => 'Status',
            'value'          => fn($m) => $m->status_id == 1
                ? Html::tag('span', 'Active',     ['class' => 'badge badge-success'])
                : Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Action',
            'template'       => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'buttons'        => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-price',
                    'data-url' => Url::to(['/productprice/productprice/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-price-js',
                    'data-url'  => Url::to(['/productprice/productprice/delete', 'id' => $m->id]),
                    'data-name' => ($m->product->name ?? '-') . ' (min qty: ' . $m->min_qty . ')',
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- =====================================================
     DISCOUNT SECTION
====================================================== -->
<?php Pjax::begin(['id' => 'discount-pjax']); ?>

<div class="mb-3 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-percent"></i>&nbsp; Discounts
            </div>
            <small class="text-muted">Discount rules applied to products in this price list</small>
        </div>
        <?= Html::button('<i class="fa fa-plus"></i> Add Discount', [
            'class'    => 'btn btn-warning btn-sm px-3 rounded-pill shadow-sm create-discount',
            'data-url' => Url::to(['/productprice/productdiscount/create', 'price_list_id' => $model->id]),
        ]) ?>
    </div>
    <?= $this->render('@backend/modules/productprice/views/productdiscount/_search', [
        'model' => $discountSearchModel,
    ]) ?>
</div>

<div style="overflow-x:auto; width:100%;">
<?= GridView::widget([
    'dataProvider' => $discountProvider,
    'hover'            => true,
    'resizableColumns' => false,
    'export'           => false,
    'tableOptions'     => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout'           => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn', 'header' => 'No'],
        [
            'attribute' => 'product_id',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->product?->name ?? ''),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['/productprice/productdiscount/view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute'      => 'discount_type',
            'label'          => 'Type',
            'format'         => 'raw',
            'value'          => fn($m) => $m->discount_type === 'percent'
                ? Html::tag('span', 'Percent', ['class' => 'badge badge-info'])
                : Html::tag('span', 'Amount',  ['class' => 'badge badge-secondary']),
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'discount_value',
            'label'          => 'Value',
            'format'         => 'raw',
            'value'          => fn($m) => $m->discount_type === 'percent'
                ? Yii::$app->formatter->asDecimal($m->discount_value, 2) . '%'
                : Yii::$app->formatter->asDecimal($m->discount_value, 2),
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions'  => ['class' => 'text-white text-right'],
        ],
        [
            'attribute'      => 'min_qty',
            'label'          => 'Min Qty',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'valid_from',
            'label'          => 'Valid From',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'valid_to',
            'label'          => 'Valid To',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'is_stackable',
            'label'          => 'Stackable',
            'format'         => 'raw',
            'value'          => fn($m) => $m->is_stackable
                ? Html::tag('span', 'Yes', ['class' => 'badge badge-success'])
                : Html::tag('span', 'No',  ['class' => 'badge badge-secondary']),
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'attribute'      => 'status_id',
            'format'         => 'raw',
            'label'          => 'Status',
            'value'          => fn($m) => $m->status_id == 1
                ? Html::tag('span', 'Active',     ['class' => 'badge badge-success'])
                : Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-white text-center'],
        ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Action',
            'template'       => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'buttons'        => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-discount',
                    'data-url' => Url::to(['/productprice/productdiscount/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-discount-js',
                    'data-url'  => Url::to(['/productprice/productdiscount/delete', 'id' => $m->id]),
                    'data-name' => ($m->product->name ?? 'All Products') . ' (' . $m->discount_type . ': ' . $m->discount_value . ')',
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODALS: PRODUCT PRICE
======================================================== -->
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePriceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-price-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-price-form" method="post">
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

<!-- =======================================================
     MODALS: DISCOUNT
======================================================== -->
<div class="modal fade" id="discountModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteDiscountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-discount-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-discount-form" method="post">
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

<?php
$this->registerJs(<<<JS

$(document).on('click', '.view-data', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});    

/* =========================================================
 * PRODUCT PRICE — ADD/EDIT/DELETE
 * ======================================================= */
$(document).on('click', '.create-price, .edit-price', function () {
    $('#priceModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#priceModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#productprice-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#priceModal').modal('hide');
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

$(document).on('click', '.delete-price-js', function () {
    $('#delete-price-name').text($(this).data('name'));
    $('#delete-price-form').attr('action', $(this).data('url'));
    $('#deletePriceModal').modal('show');
});

$(document).on('submit', '#delete-price-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deletePriceModal').modal('hide');
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

/* =========================================================
 * DISCOUNT — ADD/EDIT/DELETE
 * ======================================================= */
$(document).on('click', '.create-discount, .edit-discount', function () {
    $('#discountModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#discountModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#productdiscount-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#discountModal').modal('hide');
            $.pjax.reload({container: '#discount-pjax', timeout: 500}).done(function () {
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

$(document).on('click', '.delete-discount-js', function () {
    $('#delete-discount-name').text($(this).data('name'));
    $('#delete-discount-form').attr('action', $(this).data('url'));
    $('#deleteDiscountModal').modal('show');
});

$(document).on('submit', '#delete-discount-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deleteDiscountModal').modal('hide');
        $.pjax.reload({container: '#discount-pjax', timeout: 500}).done(function () {
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