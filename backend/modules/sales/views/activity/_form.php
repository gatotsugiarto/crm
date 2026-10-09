<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\modules\sales\models\Activity;
use common\modules\sales\models\Contact;
use common\modules\sales\models\Opportunity;

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

        <?php
        // defaults for a new activity: about an account, now, the user's own team
        if ($isNew && $model->relatedTo === null) {
            $model->relatedTo = Activity::RELATED_ACCOUNT;
            $model->activity_date = $model->activity_date ?: date('Y-m-d H:i:s');
            $model->assigned_to = $model->assigned_to ?: (Yii::$app->user->identity->team_id ?? null);
            $model->activity_type = $model->activity_type ?: Activity::ACTIVITY_TYPE_TASK;
            $model->priority = $model->priority ?: Activity::PRIORITY_NORMAL;
        }
        $isLead = $model->relatedTo === Activity::RELATED_LEAD;
        $contacts = $model->account_id
            ? Contact::find()->select(['fullname', 'id'])->where(['account_id' => $model->account_id])->indexBy('id')->column() : [];
        $opportunities = $model->account_id
            ? Opportunity::find()->select(['name', 'id'])->where(['account_id' => $model->account_id])->orderBy(['id' => SORT_DESC])->indexBy('id')->column() : [];
        $dateTime = fn($attr) => $form->field($model, $attr)->input('datetime-local', [
            'value' => Activity::toInputDateTime($model->$attr),
        ]);
        ?>

        <!-- what the activity is about -->
        <?= $form->field($model, 'relatedTo')->radioList([
            Activity::RELATED_ACCOUNT => 'Account (customer / prospect already converted)',
            Activity::RELATED_LEAD    => 'Lead (not converted yet)',
        ], ['class' => 'activity-related', 'itemOptions' => ['class' => 'mr-1', 'labelOptions' => ['class' => 'mr-4 font-weight-normal']]]) ?>

        <div class="row activity-for-account" <?= $isLead ? 'style="display:none"' : '' ?>>
            <div class="col-md-4">
                <?= $form->field($model, 'account_id')->widget(Select2::class, [
                    'data' => \common\modules\sales\models\Account::dropdown() ?? [],
                    'options' => ['placeholder' => 'Account', 'id' => 'activity-account_id'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'contact_id')->widget(Select2::class, [
                    'data' => $contacts,
                    'options' => ['placeholder' => 'Contact (optional)', 'id' => 'activity-contact_id'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'opportunity_id')->widget(Select2::class, [
                    'data' => $opportunities,
                    'options' => ['placeholder' => 'Opportunity (optional)', 'id' => 'activity-opportunity_id'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <div class="row activity-for-lead" <?= $isLead ? '' : 'style="display:none"' ?>>
            <div class="col-md-8">
                <?= $form->field($model, 'reference_id')->widget(Select2::class, [
                    'data' => \common\modules\sales\models\Lead::dropdown() ?? [],
                    'options' => ['placeholder' => 'Lead', 'id' => 'activity-reference_id'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'activity_type')->widget(Select2::class, [
                    'data' => Activity::optsActivityType(),
                    'options' => ['placeholder' => 'Type', 'id' => 'activity-activity_type'],
                    'hideSearch' => true,
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'priority')->widget(Select2::class, [
                    'data' => Activity::optsPriority(),
                    'options' => ['placeholder' => 'Priority', 'id' => 'activity-priority'],
                    'hideSearch' => true,
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'assigned_to')->widget(Select2::class, [
                    'data' => \common\modules\master\models\Team::dropdownActive($model->assigned_to),
                    'options' => ['placeholder' => 'Sales Team', 'id' => 'activity-assigned_to'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <?= $form->field($model, 'subject')->textInput(['maxlength' => true, 'placeholder' => 'e.g. Presentasi demo NextSys Hospitality']) ?>

        <div class="row">
            <div class="col-md-4"><?= $dateTime('activity_date') ?></div>
            <div class="col-md-4"><?= $dateTime('due_date') ?></div>
            <div class="col-md-4"><?= $dateTime('reminder_at') ?></div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 4, 'placeholder' => 'What was discussed / what needs to be done']) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'is_completed')->checkbox(['id' => 'activity-is_completed']) ?>
            </div>
            <div class="col-md-8 activity-completed" <?= $model->is_completed ? '' : 'style="display:none"' ?>>
                <?= $dateTime('completed_at')->hint('Empty = now, when saved.') ?>
            </div>
        </div>

        <?= $form->field($model, 'outcome')->textInput(['maxlength' => true, 'placeholder' => 'Result, e.g. "Customer asks for a revised quotation"']) ?>

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

<?php
$contactsUrl = \yii\helpers\Url::to(['/sales/activity/contacts']);
$opportunitiesUrl = \yii\helpers\Url::to(['/sales/activity/opportunities']);
$this->registerJs(<<<JS
(function () {
    var form = $('#activity-form');
    // Account or Lead
    form.on('change', 'input[name="Activity[relatedTo]"]', function () {
        var lead = this.value === 'lead';
        form.find('.activity-for-lead').toggle(lead);
        form.find('.activity-for-account').toggle(!lead);
    });
    // completed -> show the completion time
    form.on('change', '#activity-is_completed', function () {
        form.find('.activity-completed').toggle(this.checked);
    });
    // account -> its contacts and opportunities
    function refill(select, url, accountId) {
        var keep = select.val();
        select.empty().append(new Option('', '', false, false));
        if (!accountId) { select.val(null).trigger('change'); return; }
        $.getJSON(url, {id: accountId}, function (items) {
            var stillValid = false;
            $.each(items, function (i, item) {
                select.append(new Option(item.text, item.id, false, false));
                if (String(item.id) === String(keep)) stillValid = true;
            });
            select.val(stillValid ? keep : null).trigger('change');
        });
    }
    form.on('change', '#activity-account_id', function () {
        refill(form.find('#activity-contact_id'), '{$contactsUrl}', this.value);
        refill(form.find('#activity-opportunity_id'), '{$opportunitiesUrl}', this.value);
    });
})();
JS);
?>
