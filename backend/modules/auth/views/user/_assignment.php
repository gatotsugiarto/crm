<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<!-- HEADER -->
<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa fa-user-shield mr-2"></i>
            Role & Permission Assignment
        </h5>
        <small class="text-muted">Please fill in the form below to assign a user.</small>
    </div>
</div>

<style>


</style>

<?php $form = ActiveForm::begin(); ?>

<!-- CARD WRAPPER -->
<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <!-- USERNAME SECTION -->
        <div class="mb-4">
            <label class="form-label fw-semibold text-uppercase small text-muted mb-1">
                Username
            </label>
            <div class="fw-semibold text-dark h6 mb-0">
                <?= Html::encode($model->username) ?>
            </div>
        </div>

        <!-- CURRENT ASSIGNMENT TABLE -->
        <?php if ($roleAssignment): ?>
            <div class="table-wrapper mb-4">
              <div class="p-3">
                <h6 class="fw-bold text-secondary mb-3">
                  <i class="fa fa-list mr-2"></i>Current Assignments
                </h6>
                <table class="custom-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Username</th>
                      <th>Roles / Permissions</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($roleAssignment as $key => $value): ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td>
                                    <?php
                                    $auth = Yii::$app->authManager;
                                    $role = $auth->getRole($key);
                                    $permission = $auth->getPermission($key);

                                    if ($role !== null) {
                                        echo Html::encode('Role');
                                    } elseif ($permission !== null) {
                                        echo Html::encode('Permission');
                                    }
                                    ?>
                                </td>
                                <td><?= Html::encode($key) ?></td>
                                <td class="text-center">
                                    <?= Html::button('<i class="fa fa-trash"></i>', [
                                        'class' => 'btn btn-sm btn-outline-danger rounded-circle delete-user',
                                        'title' => 'Remove assignment',
                                        'data-url' => Url::to(['deleteuserassignment', 'user_id' => $model->id, 'rolepermission' => $key]),
                                        'data-name' => $key,
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
              </div>
            </div>
        <?php endif; ?>

        <!-- ROLE & PERMISSION DROPDOWNS -->
        <?php
            $assigned = array_keys($roleAssignment ?? []);
            $roleOptions = array_diff(ArrayHelper::map($allRoles, 'name', 'name'), $assigned);
            $permissionOptions = array_diff(ArrayHelper::map($allPermissions, 'name', 'name'), $assigned);
        ?>

        <div class="mb-4">
            <label class="form-label fw-bold text-secondary mb-2">
                <i class="fa fa-user-tag mr-1"></i> Add Roles
            </label>
            <?= Html::dropDownList('roles', null, $roleOptions, [
                'multiple' => true,
                'class' => 'form-control',
                'size' => 6,
            ]) ?>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold text-secondary mb-2">
                <i class="fa fa-key mr-1"></i> Add Permissions
            </label>
            <?= Html::dropDownList('permissions', null, $permissionOptions, [
                'multiple' => true,
                'class' => 'form-control',
                'size' => 6,
            ]) ?>
        </div>

    </div>
</div>

<!-- ACTION BUTTONS -->
<div class="d-flex justify-content-end mt-4">

    <div class="mr-2">
        <?= Html::a(
            '<i class="fa fa-arrow-left"></i> Back',
            ['index'],
            [
                'class' => 'btn btn-outline-secondary px-4',
                'style' => 'min-width:140px;'
            ]
        ) ?>
    </div>

    <div>
        <?= Html::submitButton(
            '<i class="fa fa-save"></i> Save',
            [
                'class' => 'btn btn-primary px-4',
                'style' => 'min-width:140px;'
            ]
        ) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete roles / permissions <strong id="delete-user-name"></strong>?</p>
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

<?php
$script = <<<JS
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
JS;
$this->registerJs($script);
?>
