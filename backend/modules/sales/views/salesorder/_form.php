<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New SalesOrder' : 'Edit SalesOrder';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\SalesOrder $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new salesorder.'
                : 'Update salesorder information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'salesorder-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['salesorder/validate'],
    'action' => $isNew ? ['salesorder/create'] : ['salesorder/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'order_number')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'account_id')->widget(Select2::classname(), [
                    'data' => \common\modules\sales\models\Account::dropdown(),
                    'options' => [
                        'placeholder' => 'Account Name',
                        'id' => 'account_id',
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
                <?= $form->field($model, 'quotation_id')->widget(Select2::classname(), [
                    'data' => \common\modules\sales\models\Quotation::dropdown(),
                    'options' => [
                        'placeholder' => 'Quotation Number',
                        'id' => 'quotation_id',
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
                <?= $form->field($model, 'order_date')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'total_amount')->widget(NumberControl::class, [
                    'maskedInputOptions' => [
                        'allowMinus' => false,
                        'prefix' => 'Rp ',
                        'groupSeparator' => '.',
                        'radixPoint' => ',',
                    ],
                    'displayOptions' => [
                        'readonly' => true,
                        'class' => 'form-control',
                    ],
                    'options' => [
                        'value' => $model->total_amount ?? 0,
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => [ 'Draft' => 'Draft', 'Confirmed' => 'Confirmed', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled', ],
                    'options' => [
                        'placeholder' => 'Quotation Number',
                        'id' => 'status',
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

        <?php if (!$isNew): ?>
        <?php
        // Sales Order form (PDF): periods, RFS / PKS, installation and billing details
        $datePicker = fn($attr, $placeholder) => $form->field($model, $attr)->widget(\kartik\date\DatePicker::class, [
            'options' => ['placeholder' => $placeholder, 'id' => 'salesorder-' . $attr],
            'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
        ]);
        $defaultAddress = fn($kind) => ($a = $model->effectiveAddress($kind)) ? 'Empty = ' . $a->address_type . ' address of the account' : 'The account has no ' . ($kind === 'installation' ? 'Shipping' : 'Billing') . ' address yet';
        ?>
        <h6 class="text-primary mt-3 mb-2"><i class="fa fa-file-alt"></i> Sales Order form (PDF)</h6>
        <?php if ($model->quotation && $model->quotation->opportunity && $model->quotation->opportunity->isOtc()): ?>
            <p class="small text-muted mb-2">OTC deal (one time charge): no trial or contract period.</p>
        <?php else: ?>
        <div class="row">
            <div class="col-md-3"><?= $datePicker('trial_start', 'Trial start') ?></div>
            <div class="col-md-3"><?= $datePicker('trial_end', 'Trial end') ?></div>
            <div class="col-md-3"><?= $datePicker('contract_start', 'Contract start') ?></div>
            <div class="col-md-3"><?= $datePicker('contract_end', 'Contract end') ?></div>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6"><?= $datePicker('rfs_date', 'Ready For Service date') ?></div>
            <div class="col-md-6"><?= $form->field($model, 'pks_number')->textInput(['maxlength' => true, 'placeholder' => 'Contract (PKS) number']) ?></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'installation_address')->textarea([
                    'rows' => 2,
                    'placeholder' => 'Site name and full address, e.g. PT. Hailal Sinar Cemerlang, Jl. Yosodipuro No. 31-33, Timuran, Banjarsari, Kota Surakarta, Jawa Tengah',
                ])->hint($defaultAddress('installation') . '. Billing address = the account\'s Billing address; contacts = its primary contact.') ?>
            </div>
        </div>
        <?php
        // Address / contact pickers are hidden for now: billing comes from the account's
        // Billing address, contacts from its primary contact (columns kept, see
        // m261002_120000_sales_order_installation_text).
        ?>
        <?php endif; ?>

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
