<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

use common\modules\sales\models\AccountAddress;

$this->title = 'Account Addresses';

$subtitle = 'Overview of account address information';
$this->params['breadcrumbs'][] = $this->title;
?>


<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    //id,
    [
        'attribute' => 'account_id',
        'value' => function ($model) {
            return $model->account?->name ?? '-';
        },
    ],
    [
        'attribute' => 'address_type',
        'value' => function ($model) {
            return $model->address_type ?? '-';
        },
    ],
    [
        'attribute' => 'address',
        'value' => function ($model) {
            return $model->address ?? '-';
        },
    ],
    [
        'attribute' => 'city_id',
        'value' => function ($model) {
            return $model->city?->name ?? '-';
        },
    ],
    [
        'attribute' => 'province_id',
        'value' => function ($model) {
            return $model->province?->name ?? '-';
        },
    ],
    [
        'attribute' => 'country_id',
        'value' => function ($model) {
            return $model->country?->name ?? '-';
        },
    ],
    [
        'attribute' => 'postal_code_id',
        'value' => function ($model) {
            return $model->postalCode?->code ?? '-';
        },
    ],
    [
        'attribute' => 'status_id',
        'value' => function ($model) {
            return $model->status?->status_active ?? '-';
        },
    ],
    'created_at',
    [
        'attribute' => 'created_by',
        'value' => function ($model) {
            return $model->createdBy?->fullname ?? '-';
        },
    ],
    'updated_at',
    [
        'attribute' => 'updated_by',
        'value' => function ($model) {
            return $model->updatedBy?->fullname ?? '-';
        },
    ],
];
?>
<div id="alert-container"></div>

<div class="mb-3">

    <!-- ROW 1: TITLE (kiri) + ACTION BUTTON (kanan) -->
    <div class="d-flex justify-content-between align-items-start mb-2">
    
        <!-- TITLE + SUBTITLE -->
        <div>
            <div class="text-primary fw-bold page-title">
                <i class="fa fa-database"></i>&nbsp;&nbsp;
                <?=  Html::encode($this->title) ?>
            </div>
            <small class="text-muted">
                <?=  $subtitle ?>
            </small>
        </div>
    
        <!-- ACTION BUTTONS -->
        <div class="d-flex align-items-center">
            <?=  ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
                'bsVersion' => '4',
                'bootstrap' => true,
                'filename' => 'AccountAddress_Export_'.date('YmdHis'),
                'showColumnSelector' => false,
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => '<i class="fa fa-download"></i> Export',
                    'class' => 'btn btn-sm px-3 btn-success rounded-pill shadow-sm mr-2',
                    'encodeLabel' => false,
                ],
            ]) ?>

            <?= Html::button('<i class="fa fa-plus"></i> New Data', [
                'class' => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-data',
                'data-url' => Url::to(['create']),
            ]) ?>
        </div>
    </div>

    <!-- ROW 2: SEARCH -->
    <?=  $this->render('_search', ['model' => $searchModel]) ?>
</div>

<div style="overflow-x:auto; width:100%;">
<?=  GridView::widget([
    'dataProvider' => $dataProvider,
    // 'filterModel'  => $searchModel,
    'hover' => true,
    'resizableColumns' => false,
    'export' => false,
    'tableOptions' => [
        'class' => 'table table-hover table-striped align-middle shadow-sm'
    ],
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn', 'header' => 'No'],
        // 'id',
        // [
        //     'attribute' => 'employee_id',
        //     'format' => 'raw',
        //     'value' => fn($model) => Html::a(
        //         Html::encode($model->employee->fullname),
        //         'javascript:void(0);',
        //         [
        //             'class' => 'text-primary view-data',
        //             'data-url' => Url::to(['view', 'id' => $model->id]),
        //         ]
        //     ),
        // ],
        // [
        //     'attribute' => 'status_id',
        //     'format' => 'raw',
        //     'value' => function ($model) {
        //         if ($model->status_id == 1) {
        //             return Html::a(
        //                 Html::tag('span', 'Active', ['class' => 'badge badge-success']),
        //                 'javascript:void(0);',
        //                 [
        //                     'class' => 'text-primary nonactive-js',
        //                     'data-url' => Url::to(['nonactive', 'id' => $model->id]),
        //                     'title' => 'Non Active',
        //                     'data-title' => 'Non Active AccountAddress',
        //                     'data-name' => $model->employee->fullname,
        //                 ]
        //             );
        //         } else {
        //             return Html::a(
        //                 Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
        //                 'javascript:void(0);',
        //                 [
        //                     'class' => 'text-primary reactive-js',
        //                     'data-url' => Url::to(['reactive', 'id' => $model->id]),
        //                     'title' => 'Reactivate',
        //                     'data-title' => 'Reactivate AccountAddress',
        //                     'data-name' => $model->employee->fullname,
        //                 ]
        //             );
        //         }
        //     },
        //     'filterType' => GridView::FILTER_SELECT2,
        //     'filter' => [1 => 'Active', 2 => 'Non Active'],
        //     'filterWidgetOptions' => [
        //         'pluginOptions' => [
        //             'allowClear' => true,
        //             'placeholder' => 'All Status',
        //         ],
        //         'options' => ['placeholder' => 'All Status'],
        //     ],
        //     'filterInputOptions' => ['class' => 'form-control'],
        //     'contentOptions' => ['class' => 'text-center'],
        //     'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        // ],
        [
        'attribute' => 'account_id',
            'value' => function ($model) {
                return $model->account?->name ?? '-';
            },
        ],
        [
            'attribute' => 'address_type',
            'value' => function ($model) {
                return $model->address_type ?? '-';
            },
        ],
        // 'address:ntext',
        [
            'attribute' => 'address',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->address),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute' => 'city_id',
            'value' => function ($model) {
                return $model->city?->name ?? '-';
            },
        ],
        // 'province_id',
        // 'country_id',
        // 'postal_code_id',
        //'created_at',
        //'createdBy.fullname',
        //'updated_at',
        //'updatedBy.fullname'
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'buttons' => [
                'update' => fn($url, $model) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-success rounded-circle edit-data',
                    'data-url' => Url::to(['update', 'id' => $model->id]),
                    'title' => 'Edit',
                ]),
                'delete' => fn($url, $model) => Html::button('<i class="fa fa-trash"></i>', [
                    'class' => 'btn btn-sm btn-outline-danger rounded-circle delete-js',
                    'data-url' => $url,
                    'data-name' => $model->account->name . ' - ' . $model->address_type
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- =======================================================
ADD/EDIT MODAL
========================================================= -->
<div class="modal fade" id="appModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- ========================================================
DELETE MODAL
========================================================= -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-modal-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-modal-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Delete', [
                        'class' => 'btn btn-danger px-4',
                        'style' => 'min-width:140px;',
                        'id' => 'delete-btn'
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<!-- ========================================================
CONFIRM MODAL
========================================================= -->
<div class="modal fade" id="confirmActiveNonActiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> <span class="data-title"></span> Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <span class="data-title"></span> <strong id="data-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="data-url" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> <span class="data-title"></span>', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
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
 * ADD/EDIT JS
 * ======================================================= */
$(document).on('click', '.create-data, .edit-data', function () {
    $('#appModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#appModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#accountaddress-form', function (e) {
    e.preventDefault();

    const form = $(this);

    $.post(form.attr('action'), form.serialize(), function (res) {

        if (res && res.success) {
            $('#appModal').modal('hide');

            $.pjax.reload({
                container: '#w0-pjax',
                timeout: 500
            }).done(function () {

                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' +
                    (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '</div>'
                );
            });

        } else if (res && res.errors) {
            form.yiiActiveForm('updateMessages', res.errors, true);
        }

    }, 'json');

    return false;
});



/* =========================================================
 * DELETE JS
 * ======================================================= */
$(document).on('click', '.delete-js', function () {
    $('#delete-modal-name').text($(this).data('name'));
    $('#delete-modal-form').attr('action', $(this).data('url'));
    $('#confirmDeleteModal').modal('show');
});

$(document).on('submit', '#delete-modal-form', function(e) {
    e.preventDefault();
    var form = $(this);
    $.post(form.attr('action'), { _csrf: yii.getCsrfToken() }, function(res) {
        $('#confirmDeleteModal').modal('hide');
        $.pjax.reload({container: '#w0-pjax', timeout: 500}).done(function() {
            if (res && res.success) {
                var html = '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                           '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                           '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                           '<span aria-hidden="true">&times;</span></button>' +
                           '</div>';
                $('#alert-container').html(html);
            }
        });
    }, 'json').fail(function() {
        alert('Request failed. Check console for details.');
    });
});

/* =========================================================
 * ACTIVE-NONACTIVE JS
 * ======================================================= */
$(document).on('click', '.nonactive-js, .reactive-js', function (e) {
    e.preventDefault();

    var url = $(this).data('url');
    var name = $(this).data('name');
    var title = $(this).data('title');

    $('.data-title').text(title);
    $('#data-name').text(name);
    $('#data-url').attr('action', url);
    $('#confirmActiveNonActiveModal').modal('show');
});


JS);
?>
