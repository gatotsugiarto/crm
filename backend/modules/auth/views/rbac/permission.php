<?php

use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = "Permissions";
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="mb-0 text-primary fw-bold"><?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the permission data registered in the system.</small>
    </div>
    <div>
        <?= Html::a('<i class="fa fa-plus"></i> Create Permission', ['createpermission'], [
            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm',
            'style' => 'min-width:120px',
        ]) ?>
    </div>
</div>

<?php Pjax::begin(['id' => 'w0-pjax', 'enablePushState' => false]); ?>

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
        [
            'attribute' => 'description',
            'format' => 'ntext',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => ['style' => 'vertical-align: middle;'],
        ],
        [
            'attribute' => 'rule_name',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => ['style' => 'vertical-align: middle;'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{delete}',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => [
                'class' => 'text-center',
                'style' => 'white-space: nowrap; vertical-align: middle;',
            ],
            'buttons' => [
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
            ],
        ],
    ],
]) ?>

<?php Pjax::end(); ?>

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

