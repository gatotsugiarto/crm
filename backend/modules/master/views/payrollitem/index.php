<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\select2\Select2;

$this->title = "Payroll Components";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    // 'id',
    'code',
    'name',
    'category_id',
    'type',
    'status.status_active',
    //'sign',
    //'default_value',
    //'taxable',
    //'display_order',
    //'percent',
    //'cap',
    //'salary_type',
    

    'created_at',
    [
        'attribute' => 'created_by',
        'value' => 'createdBy.fullname',
    ],
    'updated_at',
    [
        'attribute' => 'updated_by',
        'value' => 'updatedBy.fullname',
    ],
];
?>

<!-- ALERT CONTAINER -->
<div id="alert-container"></div>

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">Configure salary components used in payroll calculations.</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'Payroll_Components_Export_'.date('YmdHis'),
            'showColumnSelector' => false,
            'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_EXCEL => false,
            ],
            'dropdownOptions' => [
                'label' => '<i class="fa fa-download"></i> Export',
                'class' => 'btn btn-sm btn-success rounded-pill shadow-sm',
                'style' => 'min-width:160px;',
                'encodeLabel' => false,
            ],
        ]) ?>

        <?= Html::button('<i class="fa fa-plus"></i> New Component', [
            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm create-data',
            'style' => 'min-width:160px;',
            'data-url' => Url::to(['create']),
        ]) ?>
    </div>
</div>

<!-- GRIDVIEW -->
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
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
        // 'code',
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->name),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-payroll-item',
                    'data-url' => Url::to(['view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute' => 'category_id',
            'value' => 'category.name',
            'filter' => yii\helpers\ArrayHelper::map(\common\modules\master\models\PayrollCategory::find()->orderBy('id')->asArray()->all(),'id','name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => 'Payroll Category',
                ],
                'options' => ['placeholder' => 'Payroll Category'],
            ],
            'filterInputOptions' => ['class' => 'form-control'],
            'contentOptions' => ['class' => 'text-left'],
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        'type',
        //'sign',
        //'affects_gross_tax',
        //'taxable',
        //'display_order',
        //'percent',
        //'cap',
        //'salary_type',
        // [
        //     'attribute' => 'company',
        //     'format' => 'raw',
        //     'value' => fn($model) => Html::a(
        //         Html::encode($model->company),
        //         'javascript:void(0);',
        //         [
        //             'class' => 'text-primary view-company',
        //             'data-url' => Url::to(['view', 'id' => $model->id]),
        //         ]
        //     ),
        // ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->status_id == 1) {
                    return Html::a(
                        Html::tag('span', 'Active', ['class' => 'badge badge-success']),
                        'javascript:void(0);',
                        [
                            'class' => 'text-primary nonactive-js',
                            'data-url' => Url::to(['nonactive', 'id' => $model->id]),
                            'title' => 'Non Active',
                            'data-name' => $model->name,
                            'data-title' => 'Non Active Salary',
                        ]
                    );
                } else {
                    return Html::a(
                        Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
                        'javascript:void(0);',
                        [
                            'class' => 'text-primary reactive-js',
                            'data-url' => Url::to(['reactive', 'id' => $model->id]),
                            'title' => 'Reactivate',
                            'data-name' => $model->name,
                            'data-title' => 'Non Active Salary',
                        ]
                    );
                }
            },
            'filterType' => GridView::FILTER_SELECT2,
            // 'filter' => [10 => 'Active', 9 => 'Non Active', 0 => 'Delete'],
            'filter' => yii\helpers\ArrayHelper::map(\common\modules\master\models\StatusActive::find()->orderBy('id')->asArray()->all(),'id','status_active'),
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => 'All Status',
                ],
                'options' => ['placeholder' => 'All Status'],
            ],
            'filterInputOptions' => ['class' => 'form-control'],
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'buttons' => [
                'update' => fn($url, $model) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle edit-data',
                    'data-url' => Url::to(['update', 'id' => $model->id]),
                ]),
                'delete' => fn($url, $model) => Html::button('<i class="fa fa-trash"></i>', [
                    'class' => 'btn btn-sm btn-outline-danger rounded-circle delete-js',
                    'data-url' => $url,
                    'data-name' => $model->name,
                ]),
            ],
        ],
    ],
]) ?>

<?php Pjax::end(); ?>


<!-- APP MODAL -->
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

<!-- CHANGE STATUS MODAL -->
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form id="change-status-form" method="post">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fa fa-sync"></i> Change Status</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="change-status-message"></div>
                <div class="modal-footer">
                    <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                        'class' => 'btn btn-outline-secondary mr-2 px-4',
                        'data-dismiss' => 'modal',
                        'style' => 'min-width:140px;',
                    ]) ?>
                    <?= Html::submitButton('<i class="fa fa-exchange"></i> Change', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                        'id' => 'change-status-btn'
                    ]) ?>
                </div>
            </form>
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
// ====================== JAVASCRIPT ======================
$this->registerJs(<<<JS
// CREATE / EDIT MODAL
$(document).on('click', '.create-data, .edit-data', function() {
    $('#appModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#appModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#payroll-item-form', function (e) {
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

// VIEW MODAL
$(document).on('click', '.view-payroll-item', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
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

// CHANGE STATUS MODAL
$(document).on('click', '.change-status', function() {
    $('#change-status-message').html('Change status of <strong>' + $(this).data('name') + '</strong>?');
    $('#change-status-form').attr('action', $(this).data('url'));
    $('#changeStatusModal').modal('show');
});

// CHANGE STATUS - AJAX
$(document).on('submit', '#change-status-form', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#changeStatusModal').modal('hide');
                $.pjax.reload({container: '#w0-pjax', timeout: 500}).done(function() {
                    var html = '<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">' +
                               '<i class="fa fa-sync"></i> ' + (res.message || 'Status changed.') +
                               '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                               '<span aria-hidden="true">&times;</span></button>' +
                               '</div>';
                    $('#alert-container').html(html);
                    setTimeout(function() { $('.alert').alert('close'); }, 4000);
                });
            }
        },
        error: function(xhr) { console.log(xhr.responseText); }
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
