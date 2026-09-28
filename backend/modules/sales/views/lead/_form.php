<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Lead' : 'Edit Lead';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Lead $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new lead.'
                : 'Update lead information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'lead-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['lead/validate'],
    'action' => $isNew ? ['lead/create'] : ['lead/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <?= $form->field($model, 'is_converted')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'converted_account_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'converted_contact_id')->hiddenInput()->label(false) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'lead_source')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'industry')->textInput(['maxlength' => true]) ?>
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
                        'placeholder' => 'Customer Segment (optional)',
                        'id' => 'lead-customer_segment',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                ])->hint('Copied to the Account on Convert.') ?>
            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
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
                <?= $form->field($model, 'owner_user_id')->widget(Select2::classname(), [
                    'data' => \common\modules\master\models\Team::dropdown(),
                    'options' => [
                        'placeholder' => 'Sales Team',
                        // only a Sales Manager may reassign an existing record
                        'disabled' => !$isNew && !\common\components\rbac\SalesAccess::canAssign(),
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

            <div class="col-md-6">
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
