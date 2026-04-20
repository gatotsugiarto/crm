<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Opportunity $model */


$this->title = 'Detail '.'Opportunities';
$sub_title = 'Detailed insights into this opportunity and its progress';
$this->params['breadcrumbs'][] = ['label' => 'Opportunities', 'url' => ['index']];
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
                <span><small><?= Html::encode($model->account?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Contact</span><br>
                <span><small><?= Html::encode($model->contact?->fullname ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Owner User</span><br>
                <span><small><?= Html::encode($model->team?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Name</span><br>
                <span><small><?= Html::encode($model->name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Stage</span><br>
                <span><small><?= Html::encode($model->stage) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Amount</span><br>
                <span><small><?= Yii::$app->formatter->asCurrency($model->amount) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Close date</span><br>
                <span><small><?= Html::encode($model->close_date) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Probability (%)</span><br>
                <span><small><?= Html::encode($model->probability) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Description</span><br>
                <span><small><?= Html::encode($model->description) ?></small></span>
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
     OPPORTUNITY PRODUCT SECTION
====================================================== -->
<?php Pjax::begin(['id' => 'pp-pjax']); ?>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-tags"></i>&nbsp; Opportunity Products
            </div>
            <small class="text-muted">Manage products associated with each sales opportunity</small>
        </div>
        <?= Html::button('<i class="fa fa-plus"></i> Add Opportunity Product', [
            'class'    => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-opportunity-product',
            'data-url' => Url::to(['/sales/opportunityproduct/create', 'opportunity_id' => $model->id]),
        ]) ?>
    </div>
    <?= $this->render('@backend/modules/sales/views/opportunityproduct/_search', [
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
                Html::encode($model->product->name),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['/sales/opportunityproduct/view', 'id' => $model->id]),
                ]
            ),
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
            'buttons'        => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-opportunity-product',
                    'data-url' => Url::to(['/sales/opportunityproduct/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-opportunity-product-js',
                    'data-url'  => Url::to(['/sales/opportunityproduct/delete', 'id' => $m->id]),
                    'data-name' => ($m->opportunity->name ?? '-'),
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- =======================================================
     MODALS: ACCOUNT ADDRESS
======================================================== -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODALS: OPPORTUNITY PRODUCT
======================================================== -->
<div class="modal fade" id="opportunityproductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteOpportunityProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-opportunity-product-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-opportunity-product-form" method="post">
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

/* =========================================================
 * VIEW JS
 * ======================================================= */
$(document).on('click', '.view-data', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});    

/* =========================================================
 * OPPORTUNITY PRODUCT — ADD/EDIT/DELETE
 * ======================================================= */
$(document).on('click', '.create-opportunity-product, .edit-opportunity-product', function () {
    $('#opportunityproductModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#opportunityproductModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#opportunityproduct-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#opportunityproductModal').modal('hide');
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

$(document).on('click', '.delete-opportunity-product-js', function () {
    $('#delete-opportunity-product-name').text($(this).data('name'));
    $('#delete-opportunity-product-form').attr('action', $(this).data('url'));
    $('#deleteOpportunityProductModal').modal('show');
});

$(document).on('submit', '#delete-opportunity-product-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deleteOpportunityProductModal').modal('hide');
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
