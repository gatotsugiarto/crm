<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New OpportunityStageHistory' : 'Edit OpportunityStageHistory';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\OpportunityStageHistory $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new opportunitystagehistory.'
                : 'Update opportunitystagehistory information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'opportunitystagehistory-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['opportunitystagehistory/validate'],
    'action' => $isNew ? ['opportunitystagehistory/create'] : ['opportunitystagehistory/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'opportunity_id')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'old_stage')->dropDownList([ 'Prospecting' => 'Prospecting', 'Qualification' => 'Qualification', 'Proposal' => 'Proposal', 'Negotiation' => 'Negotiation', 'Closed Won' => 'Closed Won', 'Closed Lost' => 'Closed Lost', ], ['prompt' => '']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'new_stage')->dropDownList([ 'Prospecting' => 'Prospecting', 'Qualification' => 'Qualification', 'Proposal' => 'Proposal', 'Negotiation' => 'Negotiation', 'Closed Won' => 'Closed Won', 'Closed Lost' => 'Closed Lost', ], ['prompt' => '']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'changed_at')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'changed_by')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'days_in_previous_stage')->textInput() ?>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <?php if(!$isNew): ?>
                    <?= $form->field($model, 'status_id')->widget(Select2::classname(), [
                        'data' => \common\modules\master\models\Status::dropdown(),
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
