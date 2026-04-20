<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Role Assignment' : 'Role Assignment';
$icon = $isNew ? 'fa-user-shield' : 'fa-user-shield';
?>

<style>
    .select2-rows-5 .select2-results__options { 
        max-height: calc(5 * 28px); /* 5 rows × tinggi per item */ 
        overflow-y: auto; 
    }
    .select2-selection__rendered { 
        white-space: normal !important; 
        word-break: break-word; 
    }

    .select2-container--krajee .select2-selection {
        border: 0px solid #ccc;
        border-radius: 0;       /* flat */
    }
    .select2-container--krajee.select2-container--focus .select2-selection {
        border-color: #ff9800;  /* oranye saat fokus */
    }
</style>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new payroll profile.'
                : 'Update payroll profile information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'payroll-profile-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['rbac/validate'],
    'action' => $isNew ? ['rbac/create'] : ['rbac/roleassignment', 'id' => $model->name],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?php 
        //Html::hiddenInput('form_token', $formToken) 
        ?>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-uppercase small text-muted mb-1">Name</label>
                <div class="fw-semibold"><?= Html::encode($model->name) ?></div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-uppercase small text-muted mb-1">Description</label>
                <div><?= nl2br(Html::encode($model->description)) ?></div>
            </div>
        </div>

        <p>

        <?php if ($roleAssignment): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Assignments</span><br>
                <div class="card">
                  <div class="card-body">
                    <div class="table-wrapper-80">
                        <table class="ct-table">
                            <tbody>
                                <?php $i = 1; foreach ($roleAssignment as $key => $value): ?>
                                    <?php
                                    $typeLabel = $value->type === \yii\rbac\Item::TYPE_ROLE ? 'Role' : 'Permission';
                                    $badgeClass = $value->type === \yii\rbac\Item::TYPE_ROLE ? 'badge bg-primary px-2 py-1' : 'badge bg-warning px-2 py-1';
                                    $typeText = $value->type === \yii\rbac\Item::TYPE_ROLE ? 'text-warning' : 'text-info';
                                    ?>
                                    <tr>
                                        <td class="card-category text-left">
                                            <?= Html::a(
                                                '<i class="fa fa-trash"></i>',
                                                ['deleteroleassign', 'parent' => $model->name, 'name' => $key],
                                                ['class' => 'btn btn-xs btn-outline-danger rounded-circle']
                                            ) ?>
                                            &nbsp;&nbsp;
                                            <?= Html::encode($key) ?>
                                            &nbsp;&nbsp;
                                            <span class="<?= $typeText ?>">[<?= $typeLabel ?>]</span>
                                            <!-- <span class="<?= $badgeClass ?>"><?= $typeLabel ?></span> -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                  </div>
                </div>

            </div>
        </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Add Roles</span><br>
                <div class="card">
                    <?php
                    // Roles Select
                    $rolepermissionAssignments = array_keys($roleAssignment ?? []);
                    $roles = [];

                    foreach ($allRoles as $role) {
                        if ($role->name !== $model->name) {
                            $roles[$role->name] = $role->name;
                        }
                    }

                    $roleOptions = array_diff($roles, $rolepermissionAssignments);
                    ?>

                    <!-- ADD ROLES -->
                    <?= Select2::widget([
                        'name' => 'roles',
                        'data' => $roleOptions,
                        'options' => [
                            'placeholder' => 'Select role ...',
                            'multiple' => true
                        ],
                    ]); ?>
                </div>

            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Add Permission</span><br>
                <div class="card">
                    <?php
                    // Permissions Select
                    $permissions = [];

                    foreach ($allPermissions as $permission) {
                        if (strpos($permission->name, '*') === false) {
                            $permissions[$permission->name] = $permission->name;
                        }
                    }

                    $permissionOptions = array_diff($permissions, $rolepermissionAssignments);
                    ?>

                    <!-- ADD PERMISSIONS -->
                    <?= Select2::widget([
                        'name' => 'permissions',
                        'data' => $permissionOptions,
                        'options' => [
                            'placeholder' => 'Select permissions ...',
                            'multiple' => true
                        ],
                    ]); ?>
                </div>

            </div>
        </div>

        <!-- HIDDEN FIELDS -->
        <?= $form->field($model, 'rule_name')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'data')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>

    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <!-- LEFT: Back / Close -->
    <div>
        <?php if (Yii::$app->request->isAjax): ?>

            <?= Html::button('<i class="fa fa-times"></i> Close', [
                'class' => 'btn btn-outline-secondary px-4',
                'data-dismiss' => 'modal',
                'style' => 'min-width:140px;',
            ]) ?>

        <?php else: ?>

            <?= Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
                'class' => 'btn btn-outline-secondary px-4',
                'style' => 'min-width:140px;',
            ]) ?>

        <?php endif; ?>
    </div>

    <!-- RIGHT: Submit -->
    <div>
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', [
            'class' => 'btn btn-primary px-4',
            'style' => 'min-width:140px;',
        ]) ?>
    </div>

</div>


<?php ActiveForm::end(); ?>
    
