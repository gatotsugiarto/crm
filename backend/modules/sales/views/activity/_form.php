<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Activity' : 'Edit Activity';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Activity $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <small class="text-muted">
            <?= $isNew
                ? 'Please fill in the form below to register a new activity.'
                : 'Update activity information below.' ?>
        </small>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'activity-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['activity/validate'],
    'action' => $isNew ? ['activity/create'] : ['activity/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'account_id')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'contact_id')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'opportunity_id')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'reference_type')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'reference_id')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'assigned_to')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'activity_type')->dropDownList([ 'Call' => 'Call', 'Meeting' => 'Meeting', 'Email' => 'Email', 'Task' => 'Task', 'Note' => 'Note', ], ['prompt' => '']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'priority')->dropDownList([ 'Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High', 'Urgent' => 'Urgent', ], ['prompt' => '']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'activity_date')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'due_date')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'reminder_at')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'is_completed')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'completed_at')->textInput() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'outcome')->textInput(['maxlength' => true]) ?>
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
