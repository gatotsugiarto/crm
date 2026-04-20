<?php

use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = "Members";
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="mb-0 text-primary fw-bold"><?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the member data registered in the system.</small>
    </div>
    <div>
        <?= Html::button('<i class="fa fa-plus"></i> New Member', [
            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm create-member',
            'style' => 'min-width:160px;',
            'data-url' => Url::to(['create']),
        ]) ?>
    </div>
</div>

<div class="flash-message">
    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
        <div class="alert alert-<?= $key ?> alert-dismissible fade show mt-2" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
</div>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

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
                        'class' => 'text-primary view-member',
                        'data-url' => Url::to(['view', 'id' => $model->id]),
                        'title' => 'View Details of ' . $model->fullname,
                    ]
                );
            },
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
        ],
        [
            'attribute' => 'company_id', 
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
                if ($model->status) {
                    return Html::a(
                        Html::tag('span', 'Active', ['class' => 'badge bg-success']),
                        'javascript:void(0);',
                        [
                            'class' => 'text-primary suspend-member',
                            'data-url' => Url::to(['suspend', 'id' => $model->id]),
                            'title' => 'Suspend',
                            'data-name' => $model->fullname,
                        ]
                    );
                } else {
                    return Html::a(
                        Html::tag('span', 'Non Active', ['class' => 'badge bg-secondary']),
                        'javascript:void(0);',
                        [
                            'class' => 'text-primary reactive-member',
                            'data-url' => Url::to(['reactive', 'id' => $model->id]),
                            'title' => 'Reactivate',
                            'data-name' => $model->fullname,
                        ]
                    );
                }
            },
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            // 'template' => '{update} {delete} {reset} {changepassword}',
            'template' => '{update} {delete} {reset}',
            'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'buttons' => [
                'update' => fn($url, $model) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle edit-member',
                    'data-url' => Url::to(['update', 'id' => $model->id]),
                    'title' => 'Edit',
                ]),
                'delete' => fn($url, $model) => Html::button('<i class="fa fa-trash"></i>', [
                    'class' => 'btn btn-sm btn-outline-danger rounded-circle delete-member',
                    'data-url' => $url,
                    'data-name' => $model->fullname,
                    'title' => 'Delete',
                ]),
                'reset' => fn($url, $model) => Html::button('<i class="fa fa-refresh"></i>', [
                    'class' => 'btn btn-sm btn-outline-danger rounded-circle reset-member',
                    'data-url' => $url,
                    'data-name' => $model->fullname,
                    'title' => 'Reset Password',
                ]),
                'changepassword' => fn($url, $model) => Html::a('<i class="fa fa-key"></i>', 'javascript:void(0);', [
                    'class' => 'btn btn-sm btn-outline-warning rounded-circle changepassword-member',
                    'data-url' => Url::to(['changepassword', 'id' => $model->id]),
                    'title' => 'Change Password',
                ]),
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
                <p>Are you sure you want to delete member <strong id="delete-member-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-member-form" method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmSuspendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Suspend Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to suspend member <strong id="suspend-member-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="suspend-member-form" method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    <button type="submit" class="btn btn-danger">Suspend</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmReactiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Re Active Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to re active member <strong id="reactive-member-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="reactive-member-form" method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    <button type="submit" class="btn btn-danger">Re Active</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmResetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Reset Password Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset password member <strong id="reset-member-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="reset-member-form" method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    <button type="submit" class="btn btn-danger">Reset Password</button>
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
$(document).on('click', '.create-member, .edit-member', function() {
    var url = $(this).data('url');
    $('#appModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#appModal').modal('show');

    $.get(url, function(response) {
        $('#appModal .modal-body').html(response);
        bindFormAjax();
    });
});

function bindFormAjax() {
  $('#member-form').off('submit').on('submit', function(e) {
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
                    $('#appModal').modal('hide');
                    form[0].reset();
                    $('.flash-message').html('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">' + response.message +'</div>');
                    $.pjax.reload({container:'#w0-pjax'});
                } else {
                    console.log('Response errors:', response.errors);

                    $.each(response.errors, function (key, messages) {
                        var input = form.find('[name="ChangePasswordMember[' + key + ']"]');
                        if (input.length) {
                            input.addClass('is-invalid');

                            var container = input.closest('.field-changepasswordmember-' + key);
                            var hasFeedback = container.find('.invalid-feedback').length > 0;
                            
                            if (!hasFeedback) {
                                container.append('<div class="invalid-feedback">' + messages[0] + '</div>');
                            }
                        }
                    });
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                alert('Terjadi kesalahan AJAX. Cek console untuk detailnya.');
            }
        });
        return false;
    });
      
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
              //   var input = form.find('[name="ChangePasswordMember[' + key + ']"]');
              //   if (input.length) {
              //     input.addClass('is-invalid');

              //     var container = input.closest('.field-changepasswordmember-' + key);
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

$(document).on('click', '.changepassword-member', function() {
    var url = $(this).data('url');
    $('#changePasswordModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#changePasswordModal').modal('show');

    $.get(url, function(response) {
        $('#changePasswordModal .modal-body').html(response);
        bindFormAjax();
    });
});

$(document).on('click', '.view-member', function(e) {
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

$(document).on('click', '.delete-member', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#delete-member-name').text(name);
    $('#delete-member-form').attr('action', url);
    $('#confirmDeleteModal').modal('show');
});

$(document).on('submit', '#delete-member-form', function(e) {
    e.preventDefault();
    var url = $(this).attr('action');

    $.post(url, function(response) {
        if (response.success) {
            $('#confirmDeleteModal').modal('hide');
            $('.flash-message').html('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Member deleted successfully.</div>');
            $.pjax.reload({container:'#w0-pjax'});
        }
    });
});

$(document).on('click', '.suspend-member', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#suspend-member-name').text(name);
    $('#suspend-member-form').attr('action', url);
    $('#confirmSuspendModal').modal('show');
});

$(document).on('submit', '#suspend-member-form', function(e) {
    e.preventDefault();
    var url = $(this).attr('action');

    $.post(url, function(response) {
        if (response.success) {
            $('#confirmSuspendModal').modal('hide');
            $('.flash-message').html('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Member suspend successfully.</div>');
            $.pjax.reload({container:'#w0-pjax'});
        }
    });
});

$(document).on('click', '.reactive-member', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#reactive-member-name').text(name);
    $('#reactive-member-form').attr('action', url);
    $('#confirmReactiveModal').modal('show');
});

$(document).on('submit', '#reactive-member-form', function(e) {
    e.preventDefault();
    var url = $(this).attr('action');

    $.post(url, function(response) {
        if (response.success) {
            $('#confirmReactiveModal').modal('hide');
            $('.flash-message').html('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Member reactive successfully.</div>');
            $.pjax.reload({container:'#w0-pjax'});
        }
    });
});

$(document).on('click', '.reset-member', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#reset-member-name').text(name);
    $('#reset-member-form').attr('action', url);
    $('#confirmResetModal').modal('show');
});

$(document).on('submit', '#reset-member-form', function(e) {
    e.preventDefault();
    var url = $(this).attr('action');

    $.post(url, function(response) {
        if (response.success) {
            $('#confirmResetModal').modal('hide');
            $('.flash-message').html('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Member reset password successfully.</div>');
            $.pjax.reload({container:'#w0-pjax'});
        }
    });
});

JS;
$this->registerJs($script);
?>
