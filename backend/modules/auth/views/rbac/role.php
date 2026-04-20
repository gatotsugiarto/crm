<?php

use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;

$this->title = "Roles";
?>

<?php Pjax::begin(['id' => 'w0-pjax', 'enablePushState' => false]); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'name',
    'description',
    [
        'attribute' => 'data',
        'format' => 'ntext',
        'value' => function ($model) {
            return $model->data == 1 ? 'Not show at registration' : 'Will be show at registration';
        },
    ],
];
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the role data registered in the system.</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'Role_Export_' . date('YmdHis'),
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

        <?= Html::button('<i class="fa fa-plus"></i> New Role', [
            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm create-data',
            'style' => 'min-width:160px;',
            'data-url' => Url::to(['createrole']),
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
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(
                    Html::encode($model->name),
                    'javascript:void(0);',
                    [
                        'class' => 'text-primary view-data',
                        'data-url' => Url::to(['viewrole', 'id' => $model->name]),
                        'title' => 'View Details of ' . $model->name,
                    ]
                );
            },
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => ['style' => 'vertical-align: middle;'],
        ],
        'description',
        // [
        //     'attribute' => 'rule_name',
        //     'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        //     'contentOptions' => ['style' => 'vertical-align: middle;'],
        // ],
        // [
        //     'attribute' => 'data',
        //     'format' => 'ntext',
        //     'value' => function ($model) {
        //         return $model->data == 1 ? 'Not show at registration' : 'Will be show at registration';
        //     },
        //     'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        //     'contentOptions' => ['style' => 'vertical-align: middle;'],
        // ],
        [
            'label' => 'Be show at registration',
            'attribute' => 'data',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                if ($model->data != 1) {
                    return '<i class="fa fa-check text-primary"></i>';
                }else{
                    return '<i class="fa fa-times text-danger"></i>';
                }
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{update} {delete} {assignment}',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => [
                'class' => 'text-center',
                'style' => 'white-space: nowrap; vertical-align: middle;',
            ],
            'buttons' => [
                'update' => fn($url, $model) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle edit-data',
                    'data-url' => Url::to(['updaterole', 'id' => $model->name]),
                    'title' => 'Edit',
                ]),
                'delete' => function ($url, $model, $key) {
                    $url = Url::toRoute(['deletepermission', 'id' => $key]);
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                        'title' => 'Delete Permission',
                        'class' => 'btn btn-sm btn-outline-danger rounded-circle',
                        'data-confirm' => 'Are you sure you want to delete this permission?',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
                },
                'assignment' => fn($url, $model) => Html::a(
                    '<i class="fa fa-tasks"></i>',
                    ['roleassignment', 'id' => $model->name],
                    [
                        'class' => 'btn btn-sm btn-info rounded-circle',
                        'title' => 'Assignment',
                    ]
                ),
            ],
        ],
    ],
]) ?>

<?php Pjax::end(); ?>

<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4">
                <!-- AJAX akan load form create/update di sini -->
            </div>
        </div>
    </div>
</div>

<!-- Modal View -->
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

$(document).on('click', '.create-data, .edit-data', function() {
    var url = $(this).data('url');
    $('#formModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#formModal').modal('show');

    $.get(url, function(response) {
        $('#formModal .modal-body').html(response);
        bindFormAjax();
    });
});

function bindFormAjax() {
  $('#company-form').off('submit').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                form.find('.invalid-feedback').remove();
                form.find('.is-invalid').removeClass('is-invalid');
                
                if (response.success) {
                    $('#formModal').modal('hide');
                    form[0].reset();
                    $('.flash-message').html('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">' + response.message +'</div>');
                    $.pjax.reload({container:'#w0-pjax'});
                } else {
                    console.log('Response errors:', response.errors);

                    
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                alert('Terjadi kesalahan AJAX. Cek console untuk detailnya.');
            }
        });
        return false;
    });
}

$(document).on('click', '.view-data', function(e) {
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