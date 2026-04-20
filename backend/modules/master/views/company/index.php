<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\select2\Select2;

$this->title = "Companies";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'code',
    'company',
    [
        'attribute' => 'status_id',
        'format' => 'raw',
        'value' => function ($model) {
            if($model){
                if($model->status_id == 1){
                    return $model->status_id ? 'Active' : 'Non Active';
                }
            }else{
                return '-';
            }
        },
        'headerOptions' => ['class' => 'text-white bg-creative text-center'],
    ],
];
?>

<!-- ALERT CONTAINER -->
<div id="alert-container"></div>

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the company data registered in the system.</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'Company_Export_'.date('YmdHis'),
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

        <?= Html::button('<i class="fa fa-plus"></i> New Company', [
            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm create-company',
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
        'code',
        [
            'attribute' => 'company',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->company),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-company',
                    'data-url' => Url::to(['view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'value' => function ($model) {
                $isActive = $model->status_id == 1;
                $label = $isActive ? 'Active' : 'Non Active';
                $class = $isActive ? 'badge badge-success px-3 py-2' : 'badge badge-dark px-3 py-2';
                $url   = Url::to(['togglestatus', 'id' => $model->id]);

                return Html::a(
                    Html::tag('span', $label, ['class' => $class . ' status-badge']),
                    'javascript:void(0);',
                    [
                        'class' => 'change-status',
                        'data-url' => $url,
                        'data-status' => $model->status_id,
                        'data-name' => $model->company,
                    ]
                );
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => [1 => 'Active', 0 => 'Non Active'],
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
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle edit-company',
                    'data-url' => Url::to(['update', 'id' => $model->id]),
                ]),
                'delete' => fn($url, $model) => Html::button('<i class="fa fa-trash"></i>', [
                    'class' => 'btn btn-sm btn-outline-danger rounded-circle delete-company',
                    'data-url' => $url,
                    'data-name' => $model->company,
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

<!-- DELETE CONFIRMATION -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-company-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-company-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Delete', [
                        'class' => 'btn btn-danger px-4',
                        'style' => 'min-width:140px;',
                        'id' => 'delete-company-btn'
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

<?php
// ====================== JAVASCRIPT ======================
$this->registerJs(<<<JS
// CREATE / EDIT MODAL
$(document).on('click', '.create-company, .edit-company', function() {
    $('#appModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#appModal').modal('show').find('.modal-body').load($(this).data('url'));
});

// VIEW MODAL
$(document).on('click', '.view-company', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});

// DELETE CONFIRMATION
$(document).on('click', '.delete-company', function() {
    $('#delete-company-name').text($(this).data('name'));
    $('#delete-company-form').attr('action', $(this).data('url'));
    $('#confirmDeleteModal').modal('show');
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

// DELETE - AJAX
$(document).on('submit', '#delete-company-form', function(e) {
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
                setTimeout(function() { $('.alert').alert('close'); }, 4000);
            }
        });
    }, 'json').fail(function() {
        alert('Request failed. Check console for details.');
    });
});
JS);
?>
