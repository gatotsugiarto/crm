<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Account' : 'Edit Account';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Account $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new account.'
                : 'Update account information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'account-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['account/validate'],
    'action' => $isNew ? ['account/create'] : ['account/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'parent_account_id')->widget(Select2::classname(), [
                    'data' => \common\modules\sales\models\Account::dropdown(),
                    'options' => [
                        'placeholder' => 'None',
                        'id' => 'parent_account_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'account_type')->widget(Select2::classname(), [
                    'data' => [ 'Prospect' => 'Prospect', 'Customer' => 'Customer', 'Partner' => 'Partner', 'Reseller' => 'Reseller', 'Vendor' => 'Vendor', ],
                    'options' => [
                        'placeholder' => 'Customer Type',
                        'id' => 'account_type',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?php
                // tags mode accepts free text; keep a saved value that isn't in the suggestion list
                $segmentData = \common\modules\sales\models\Account::optsCustomerSegment();
                if ($model->customer_segment && !isset($segmentData[$model->customer_segment])) {
                    $segmentData[$model->customer_segment] = $model->customer_segment;
                }
                ?>
                <?= $form->field($model, 'customer_segment')->widget(Select2::class, [
                    'data' => $segmentData,
                    'options' => [
                        'placeholder' => 'Customer Segment',
                        'id' => 'customer_segment',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'industry')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'tax_number')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?php if ($model->isNewRecord && !$model->country_id) { $model->country_id = \common\modules\master\models\Country::defaultId(); } ?>
                <?= $form->field($model, 'country_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\Country::dropdown(),
                    'options' => [
                        'placeholder' => 'Country',
                        'id' => 'country_id',
                        'data-dep-child' => '#province_id',
                        'data-dep-url' => \yii\helpers\Url::to(['/master/location/provinces']),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'province_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\Province::dropdownFor($model->country_id),
                    'options' => [
                        'placeholder' => 'Province',
                        'id' => 'province_id',
                        'data-dep-child' => '#city_id',
                        'data-dep-url' => \yii\helpers\Url::to(['/master/location/cities']),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'city_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\City::dropdownFor($model->province_id),
                    'options' => [
                        'placeholder' => 'City',
                        'id' => 'city_id',
                        'data-dep-child' => '#postal_code_id',
                        'data-dep-url' => \yii\helpers\Url::to(['/master/location/postalcodes']),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'postal_code_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\PostalCode::dropdownFor($model->city_id),
                    'options' => [
                        'placeholder' => 'Postal Code',
                        'id' => 'postal_code_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'price_list_id')->widget(Select2::classname(), [
                    'data' => \common\modules\productprice\models\PriceList::dropdownActive($model->price_list_id),
                    'options' => [
                        'placeholder' => 'None',
                        'id' => 'price_list_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'owner_user_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\Team::dropdownActive($model->owner_user_id),
                    'options' => [
                        'placeholder' => 'Sales Team',
                        // only a Sales Manager may reassign an existing record
                        'disabled' => !$isNew && !\common\components\rbac\SalesAccess::canAssign(),
                        // Assigned Sales lists only this team's members
                        'id' => 'account-owner_user_id',
                        'data-dep-child' => '#assigned_user_id',
                        'data-dep-url' => \yii\helpers\Url::to(['/master/lookup/team-members']),
                        // 'id' => 'status_id',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                        // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                    ],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?php
                $assignedData = \common\modules\master\models\Team::membersDropdown($model->owner_user_id);
                if ($model->assigned_user_id && !isset($assignedData[$model->assigned_user_id])) {
                    // keep showing an older assignment that is no longer a team member
                    $assignedData[$model->assigned_user_id] = $model->assignedUser?->fullname ?? $model->assigned_user_id;
                }
                ?>
                <?= $form->field($model, 'assigned_user_id')->widget(Select2::class, [
                    'data' => $assignedData,
                    'options' => [
                        'placeholder' => 'Assigned Sales',
                        'id' => 'assigned_user_id',
                        'disabled' => !$isNew && !\common\components\rbac\SalesAccess::canAssign(),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]) ?>
            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?php if(!$isNew): ?>
                    <?= $form->field($model, 'status_id')->widget(Select2::classname(), [
                        'data' => \common\modules\master\models\StatusActive::dropdown(),
                        'options' => [
                            'placeholder' => 'Status',
                            // 'id' => 'status_id',
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            // 'dropdownParent' => new \yii\web\JsExpression('$("#appModal")'), // pastikan ID sesuai modal
                            // 'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
                        ],
                    ]) ?>
                <?php else: ?>
                    <?= $form->field($model, 'status_id')->hiddenInput()->label(false) ?>
                <?php endif; ?>
            </div>
            <div class="col-md-6"></div>
        </div>

    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <!-- LEFT: Back / Close -->
    <div>
        <?php  if (Yii::$app->request->isAjax): ?>

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
