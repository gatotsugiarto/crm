<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\select2\Select2;

$this->title = "Payroll Categories";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'code',
    'name',
    'display_order',
];
?>

<!-- ALERT CONTAINER -->
<div id="alert-container"></div>

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">Organize payroll components into structured categories.</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'Payroll_Category_Export_'.date('YmdHis'),
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

        <?= Html::button('<i class="fa fa-plus"></i> New Category', [
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
        'code',
        'name',
        'display_order',
        
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


<?php
// ====================== JAVASCRIPT ======================
$this->registerJs(<<<JS
// CREATE / EDIT MODAL
$(document).on('click', '.create-data, .edit-data', function() {
    $('#appModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#appModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#payroll-category-form', function (e) {
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
JS);
?>
