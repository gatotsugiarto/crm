<?php
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;

$this->title = "Log Activities";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'controller_action',
    'model_name',
    'record_id',
    'action_by',
    'created_at',
    'ip_address',
    'user_agent',
    'request_url',
    'before_data',
    'after_data',
    'status',
    'remarks',
];
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the log activity data registered in the system.</small>
    </div>

    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'Log_Activity_'.date('YmdHis'),
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
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'panel' => false,
    'bordered' => false,
    'striped' => false,
    'condensed' => false,
    'hover' => true,
    'resizableColumns' => false,
    'export' => false,
    'responsiveWrap' => false,
    'tableOptions' => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn', 
            'header' => 'No',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        [
            'attribute' => 'remarks',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(Html::encode($model->remarks),
                    'javascript:void(0);',
                    [
                        'class' => 'text-primary view-log',
                        'data-url' => Url::to(['view', 'id' => $model->id]),
                        'title' => 'View Details of ' . $model->record_id,
                    ]
                );
            },
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],

            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        [
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => function ($model) {
                if($model->employee){
                    return $model->employee->fullname;
                }else{
                    return '-';
                }
            },
        ],
        // [
        //     'attribute' => 'controller_action', 
        //     'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        // ],
        // [
        //     'attribute' => 'model_name', 
        //     'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        // ],
        [
            'attribute' => 'action_by',
            'format' => 'raw',
            'value' => function ($model) {
                if($model){
                    return $model->user->fullname;
                }else{
                    return '-';
                }
            },
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        [
            'attribute' => 'created_at', 
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        // [
        //     'attribute' => 'record_id',
        //     'format' => 'raw',
        //     'value' => function ($model) {
        //         return Html::a(
        //             'Trx ID: '. Html::encode($model->record_id),
        //             'javascript:void(0);',
        //             [
        //                 'class' => 'text-primary view-log',
        //                 'data-url' => Url::to(['view', 'id' => $model->id]),
        //                 'title' => 'View Details of ' . $model->record_id,
        //             ]
        //         );
        //     },
        //     'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        // ],
    ],
]) ?>

<?php Pjax::end(); ?>

<!-- Modal Profile -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4">
                <!-- AJAX content here -->
            </div>
        </div>
    </div>
</div>

<?php
$script = <<<JS
$(document).on('click', '.view-log', function(e) {
    e.preventDefault();
    var url = $(this).data('url') || $(this).attr('href');
    if (!url) return;

    $('#viewModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#viewModal').modal('show');

    $.get(url, function(response) {
        $('#viewModal .modal-body').html(response);
    }).fail(function() {
        $('#viewModal .modal-body').html('<div class="alert alert-danger text-center">Failed to load content.</div>');
    });
});

// Pastikan tombol close bekerja
$(document).on('click', '[data-bs-dismiss="modal"]', function () {
    $('#viewModal').modal('hide');
});

JS;
$this->registerJs($script);
?>
