<?php

use yii\helpers\Html;
use common\modules\master\models\QuotationLayout;

/** @var yii\web\View $this */
/** @var common\modules\master\models\QuotationLayout $model */

$this->title = 'Layout Quotation';
$canEdit = Yii::$app->user->can('backend.master.quotationlayout.update')
    || Yii::$app->user->can('backend.master.quotationlayout.*') || Yii::$app->user->can('root');
$line = fn($label, $value) => '<div class="row mb-2"><div class="col-md-3 text-secondary small">' . Html::encode($label)
    . '</div><div class="col-md-9"><small>' . ($value === null || $value === '' ? '-' : $value) . '</small></div></div>';
$list = fn($text) => ($items = QuotationLayout::lines($text))
    ? '<ol class="pl-3 mb-0">' . implode('', array_map(fn($t) => '<li>' . QuotationLayout::inline($t) . '</li>', $items)) . '</ol>' : '-';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1"><i class="fa fa-file-signature"></i>&nbsp;&nbsp;<?= $this->title ?></h5>
        <p class="text-muted small mb-0">Template for the quotation letter (SPH). New quotations copy these texts; existing quotations keep theirs.</p>
    </div>
    <?php if ($canEdit): ?>
        <?= Html::a('<i class="fa fa-edit"></i> Edit Layout', ['update'], ['class' => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm']) ?>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-3"><div class="card-body">
    <h6 class="fw-bold mb-3">Letterhead</h6>
    <?= $line('Logo', $model->hasLogo() ? Html::img(['logo', 'v' => $model->logo_file], ['style' => 'max-height:60px']) : '<span class="text-muted">No logo uploaded yet</span>') ?>
    <?= $line('Company Name', Html::encode($model->company_name)) ?>
    <?= $line('Footer', Html::encode(trim($model->company_address . ' | ' . $model->company_phone . ' | ' . $model->company_website, ' |'))) ?>
</div></div>

<div class="card shadow-sm border-0 rounded-4 mb-3"><div class="card-body">
    <h6 class="fw-bold mb-3">Numbering &amp; Letter</h6>
    <?= $line('SPH Number', Html::encode('001/' . str_replace('{LINE}', 'NHS', trim($model->number_code, '/')) . '/IX/2026') . ' <span class="text-muted">(example for business line NHS; {LINE} = the quotation\'s business line; running number per line, restarts every year)</span>') ?>
    <?= $line('City', Html::encode($model->city)) ?>
    <?= $line('Recipient Title', Html::encode($model->recipient_title)) ?>
    <?= $line('Default Contract', $model->default_contract_months ? Html::encode($model->default_contract_months . ' months') : null) ?>
    <?= $line('Opening Text', nl2br(QuotationLayout::inline($model->opening_text))) ?>
    <?= $line('Terms & Conditions (Recurring)', $list($model->terms_text)) ?>
    <?= $line('Terms & Conditions (OTC)', $list($model->terms_text_otc)) ?>
    <?= $line('Installation Notes', $list($model->installation_notes)) ?>
    <?= $line('Closing Text', nl2br(QuotationLayout::inline($model->closing_text))) ?>
    <?= $line('Signatures', Html::encode($model->sign_left_label) . ' &nbsp;/&nbsp; ' . Html::encode($model->sign_right_label)) ?>
    <?= $line('Signer (Diajukan Oleh)', 'Assigned Sales of the account (name + Job Title)' . ($model->signer_name ? '<br><span class="text-muted">Fallback when none: ' . Html::encode($model->signer_name) . ($model->signer_title ? ' &mdash; ' . Html::encode($model->signer_title) : '') . '</span>' : '')) ?>
</div></div>
