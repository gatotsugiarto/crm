<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Create Permission (' . ucfirst($group) . ')';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="auth-item-index">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="d-flex flex-wrap gap-2 mb-1">
                                <?= Html::a('<i class="fa fa-server"></i> Backend', ['/auth/rbac/createpermission', 'group' => 'backend'], [
                                    'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm',
                                    'style' => 'min-width:180px',
                                ]) ?>
                                &nbsp;&nbsp;&nbsp;
                                <?= Html::a('<i class="fa fa-globe"></i> Frontend', ['/auth/rbac/createpermission', 'group' => 'frontend'], [
                                    'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm',
                                    'style' => 'min-width:180px',
                                ]) ?>
                                
                            </div>

                            <?php $form = ActiveForm::begin(['id' => 'permission-form']); ?>

                            <?php foreach ($routes as $moduleName => $moduleData): ?>
                                <div class="card-body">
                                    <div class="card-header">
                                        <h5 class="fw-bold mb-0">
                                            <i class="fa fa-cube text-secondary me-3"></i> Module: <?= Html::encode($moduleName) ?>
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="creative-table-wrapper">
                                            <table class="creative-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:50px">#</th>
                                                        <th>Permission</th>
                                                        <th>Controller / Action</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($moduleData['controllers'] ?? [] as $controllerName => $controller): ?>
                                                        <?php if ($controllerName === 'default') continue; ?>
                                                        <?php
                                                            $parentPermission = "{$group}.{$moduleName}.{$controllerName}.*";
                                                            $hasParent = isset($permissions[$parentPermission]);
                                                        ?>
                                                        <tr class="table-warning">
                                                            <td>
                                                                <?php if (!$hasParent): ?>
                                                                    <?= Html::checkbox("formpermission[{$parentPermission}]", false, ['value' => 1]) ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= Html::encode($parentPermission) ?></td>
                                                            <td>
                                                                <!-- <span class="badge bg-primary"><?= Html::encode($controllerName) ?></span> -->
                                                                <?= Html::encode($controllerName) ?>
                                                            </td>
                                                            <td></td>
                                                        </tr>

                                                        <?php foreach ($controller['actions'] ?? [] as $action): ?>
                                                            <?php
                                                                $actionPermission = "{$group}.{$moduleName}.{$controllerName}." . strtolower($action['name']);
                                                                $hasAction = isset($permissions[$actionPermission]);
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php if (!$hasAction): ?>
                                                                        <?= Html::checkbox("formpermission[{$actionPermission}]", false, ['value' => 1]) ?>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= Html::encode($actionPermission) ?></td>
                                                                <td>
                                                                    <!-- <span class="badge bg-warning text-dark"><?= Html::encode($action['name']) ?></span> -->
                                                                    <?= Html::encode($action['name']) ?>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$this->registerJs(<<<JS
    // Parent checkbox auto-selects children
    $('input[type="checkbox"]').on('change', function() {
        const name = $(this).attr('name');
        if (name.endsWith('.*]')) {
            const base = name.replace('.*]', '');
            const checked = $(this).is(':checked');

            $('input[type="checkbox"]').each(function() {
                const childName = $(this).attr('name');
                if (childName.startsWith(base + '.') && !childName.endsWith('.*]')) {
                    $(this).prop('checked', checked);
                }
            });
        }
    });

    // Bootstrap tooltip activation
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
JS);
?>
