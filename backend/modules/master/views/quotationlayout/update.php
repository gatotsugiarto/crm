<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\master\models\QuotationLayout $model */

$this->title = 'Edit Layout Quotation';
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title mb-1"><i class="fa fa-edit"></i>&nbsp;&nbsp;<?= $this->title ?></h5>
    <p class="text-muted small mb-0">Changes apply to quotations created from now on. Existing quotations keep the texts they were created with.</p>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="card shadow-sm border-0 rounded-4 mb-3"><div class="card-body">
    <h6 class="fw-bold mb-3">Letterhead</h6>
    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6">
            <?= $form->field($model, 'logoUpload')->fileInput(['accept' => '.png,.jpg,.jpeg']) ?>
            <?php if ($model->hasLogo()): ?>
                <small class="text-muted d-block mb-2">Current: <?= Html::img(['logo', 'v' => $model->logo_file], ['style' => 'max-height:40px']) ?> (upload a new file to replace it)</small>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"><?= $form->field($model, 'company_address')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'company_phone')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'company_website')->textInput(['maxlength' => true]) ?></div>
    </div>
</div></div>

<div class="card shadow-sm border-0 rounded-4 mb-3"><div class="card-body">
    <h6 class="fw-bold mb-3">Numbering &amp; Letter</h6>
    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'number_code')->textInput(['maxlength' => true])->hint('Number looks like 001/<b>' . Html::encode(str_replace('{LINE}', 'NHS', trim($model->number_code, '/'))) . '</b>/IX/2026. <code>{LINE}</code> = the quotation\'s business line (NHS, NXG, IPTV); numbers run per line.') ?></div>
        <div class="col-md-4"><?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'default_contract_months')->textInput(['type' => 'number', 'min' => 1]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'recipient_title')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'sign_left_label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'sign_right_label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'signer_name')->textInput(['maxlength' => true, 'placeholder' => 'e.g. Risma Dewi Marthen'])->hint('Only used when the account has no Assigned Sales. Normally the Assigned Sales (and their Job Title) signs.') ?></div>
        <div class="col-md-6"><?= $form->field($model, 'signer_title')->textInput(['maxlength' => true, 'placeholder' => 'e.g. Account Manager']) ?></div>
    </div>
    <?= $form->field($model, 'opening_text')->textarea(['rows' => 4]) ?>
    <?= $form->field($model, 'terms_text')->textarea(['rows' => 12])->hint('For <b>Recurring</b> deals. One clause per line; the PDF numbers them. Wrap text in <code>**...**</code> to print it bold.') ?>
    <?= $form->field($model, 'terms_text_otc')->textarea(['rows' => 7])->hint('For <b>OTC</b> deals (no contract / subscription clauses). Empty = the Recurring terms are used.') ?>
    <?= $form->field($model, 'installation_notes')->textarea(['rows' => 3])->hint('One note per line. <code>**...**</code> = bold.') ?>
    <?= $form->field($model, 'closing_text')->textarea(['rows' => 5]) ?>
</div></div>

<div class="d-flex justify-content-end mb-4">
    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-outline-secondary mr-2 px-4']) ?>
    <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-primary px-4']) ?>
</div>

<?php ActiveForm::end(); ?>
