<?php

use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;

$this->title = "User Access";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'id',
    'username',
    'fullname',
    'auth_key',
    'email',
    [
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($model) {
            if($model){
                if($model->status == 10){
                    return $model->status ? 'Active' : 'Suspend';
                }
            }else{
                return '-';
            }
        },
    ],
    [
        'attribute' => 'created_at',
        'format' => 'raw',
        'value' => function ($model) {
            if($model){
                return date('Y-m-d H:i:s', $model->created_at);
            }else{
                return '-';
            }
        },
    ],
    [
        'attribute' => 'updated_at',
        'format' => 'raw',
        'value' => function ($model) {
            if($model){
                return date('Y-m-d H:i:s', $model->updated_at);
            }else{
                return '-';
            }
        },
    ],
];
?>

<!-- ALERT CONTAINER -->
<!-- <div id="alert-container"></div> -->

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the user data registered in the system.</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'User_Export_' . date('YmdHis'),
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


        <?= Html::button('<i class="fa fa-plus"></i> New User', [
            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm create-user',
            'style' => 'min-width:160px;',
            'data-url' => Url::to(['create']),
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
            'attribute' => 'username', 
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        [
            'attribute' => 'fullname',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(
                    Html::encode($model->fullname),
                    'javascript:void(0);',
                    [
                        'class' => 'text-primary view-user',
                        'data-url' => Url::to(['view', 'id' => $model->id]),
                        'title' => 'View Details of ' . $model->fullname,
                    ]
                );
            },
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        // [
        //     'attribute' => 'email', 
        //     'format' => 'email', 
        //     'headerOptions' => ['class' => 'text-white bg-creative']
        // ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->status == 10) {
                    return Html::a(
                        Html::tag('span', 'Active', ['class' => 'badge badge-success']),
                        'javascript:void(0);',
                        [
                            'class' => 'text-primary suspend-user',
                            'data-url' => Url::to(['suspend', 'id' => $model->id]),
                            'title' => 'Suspend',
                            'data-name' => $model->fullname,
                        ]
                    );
                } else {
                    return Html::a(
                        Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
                        'javascript:void(0);',
                        [
                            'class' => 'text-primary reactive-user',
                            'data-url' => Url::to(['reactive', 'id' => $model->id]),
                            'title' => 'Reactivate',
                            'data-name' => $model->fullname,
                        ]
                    );
                }
            },
            'filterType' => GridView::FILTER_SELECT2,
            // 'filter' => [10 => 'Active', 9 => 'Non Active', 0 => 'Delete'],
            'filter' => [10 => 'Active', 9 => 'Non Active'],
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
            // 'template' => '{update} {delete} {reset} {changepassword}',
            'template' => '{update} {delete} {reset} {assignment}',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'buttons' => [
                'update' => fn($url, $model) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-primary rounded-circle edit-user',
                    'data-url' => Url::to(['update', 'id' => $model->id]),
                    'title' => 'Edit',
                ]),
                'delete' => fn($url, $model) => Html::button('<i class="fa fa-trash"></i>', [
                    'class' => 'btn btn-sm btn-outline-danger rounded-circle delete-user',
                    'data-url' => $url,
                    'data-name' => $model->fullname,
                    'title' => 'Delete',
                ]),
                'reset' => fn($url, $model) => Html::button('<i class="fa fa-refresh"></i>', [
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle reset-user',
                    'data-url' => $url,
                    'data-name' => $model->fullname,
                    'title' => 'Reset Password',
                ]),
                'changepassword' => fn($url, $model) => Html::a('<i class="fa fa-key"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle changepassword-user',
                    'data-url' => Url::to(['changepassword', 'id' => $model->id]),
                    'title' => 'Change Password',
                ]),
                'assignment' => fn($url, $model) => Html::a(
                    '<i class="fa fa-tasks"></i>',
                    ['assignment', 'id' => $model->id],
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

<div class="modal fade" id="appModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4">
                <!-- AJAX akan load form create/update di sini -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4">
                <!-- AJAX akan load form changepassword di sini -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="delete-user-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-user-form" method="post">
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

<div class="modal fade" id="confirmSuspendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Suspend Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to suspend user <strong id="suspend-user-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="suspend-user-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Suspend', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmReactiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Re Active Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to re active user <strong id="reactive-user-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>

                <form id="reactive-user-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-refresh"></i> Re Active', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmResetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Reset Password Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset password user <strong id="reset-user-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="reset-user-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-refresh"></i> Reset Password', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

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
// CREATE / EDIT MODAL
$(document).on('click', '.create-user, .edit-user', function() {
    $('#appModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#appModal').modal('show').find('.modal-body').load($(this).data('url'));
});




function bindFormAjax() {
  
      
  $('#changepassword-form').off('submit').on('submit', function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: form.serialize(),
      success: function (response) {
          form.find('.invalid-feedback').remove();
          form.find('.is-invalid').removeClass('is-invalid');

          if (response.success) {
            $('#changePasswordModal').modal('hide');
            form[0].reset();
            $('.flash-message').html(
              '<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">' +
                response.message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
              '</div>'
            );
          } else {
            // Biarkan ActiveForm render ulang
            // console.log('Response errors:', response.errors);

              // $.each(response.errors, function (key, messages) {
              //   var input = form.find('[name="ChangePasswordUser[' + key + ']"]');
              //   if (input.length) {
              //     input.addClass('is-invalid');

              //     var container = input.closest('.field-changepassworduser-' + key);
              //     var hasFeedback = container.find('.invalid-feedback').length > 0;

              //     if (!hasFeedback) {
              //       container.append('<div class="invalid-feedback">' + messages[0] + '</div>');
              //     }
              //   }
              // });
          }
        },
      error: function (xhr) {
        console.error('AJAX Error:', xhr);
        alert('Terjadi kesalahan AJAX. Cek console untuk detailnya.');
      }
    });
  });
}

$(document).on('click', '.changepassword-user', function() {
    var url = $(this).data('url');
    $('#changePasswordModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#changePasswordModal').modal('show');

    $.get(url, function(response) {
        $('#changePasswordModal .modal-body').html(response);
        bindFormAjax();
    });
});

$(document).on('click', '.view-user', function(e) {
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

$(document).on('click', '.delete-user', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#delete-user-name').text(name);
    $('#delete-user-form').attr('action', url);
    $('#confirmDeleteModal').modal('show');
});

$(document).on('submit', '#delete-user-form', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#confirmDeleteModal').modal('hide');
                $.pjax.reload({container: '#w0-pjax', timeout: 500}).done(function() {
                    var html = 
                        '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                            '<i class="fa fa-sync mr-1"></i> ' + (res.message || 'Status changed.') +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>';

                    // Ganti isi alert-container
                    $('#alert-container').html(html);
                });
            }
        },
        error: function(xhr) { console.log(xhr.responseText); }
    });
});

$(document).on('click', '.suspend-user', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#suspend-user-name').text(name);
    $('#suspend-user-form').attr('action', url);
    $('#confirmSuspendModal').modal('show');
});

$(document).on('submit', '#suspend-user-form', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#confirmSuspendModal').modal('hide');
                $.pjax.reload({container: '#w0-pjax', timeout: 500}).done(function() {
                    var html = 
                        '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                            '<i class="fa fa-sync mr-1"></i> ' + (res.message || 'Status changed.') +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>';

                    // Ganti isi alert-container
                    $('#alert-container').html(html);
                });
            }
        },
        error: function(xhr) { console.log(xhr.responseText); }
    });
});

$(document).on('beforeSubmit', '#user-form', function (e) {
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

$(document).on('click', '.reactive-user', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#reactive-user-name').text(name);
    $('#reactive-user-form').attr('action', url);
    $('#confirmReactiveModal').modal('show');
});

$(document).on('submit', '#reactive-user-form', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#confirmReactiveModal').modal('hide');
                $.pjax.reload({container: '#w0-pjax', timeout: 500}).done(function() {
                    var html = 
                        '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                            '<i class="fa fa-sync mr-1"></i> ' + (res.message || 'Status changed.') +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>';

                    // Ganti isi alert-container
                    $('#alert-container').html(html);
                });
            }
        },
        error: function(xhr) { console.log(xhr.responseText); }
    });
});

$(document).on('click', '.reset-user', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#reset-user-name').text(name);
    $('#reset-user-form').attr('action', url);
    $('#confirmResetModal').modal('show');
});

$(document).on('submit', '#reset-user-form', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#confirmResetModal').modal('hide');
                $.pjax.reload({container: '#w0-pjax', timeout: 500}).done(function() {
                    var html = 
                        '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                            '<i class="fa fa-sync mr-1"></i> ' + (res.message || 'Status changed.') +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>';

                    // Ganti isi alert-container
                    $('#alert-container').html(html);
                });
            }
        },
        error: function(xhr) { console.log(xhr.responseText); }
    });
});

JS;
$this->registerJs($script);
?>
