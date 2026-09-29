<?php

use yii\helpers\Html;
use common\modules\master\models\QuotationLayout;

/**
 * Quotation letter (SPH) body, rendered to PDF by mPDF in
 * QuotationController::actionPdf. Letterhead logo and footer are set there as
 * mPDF page header/footer so they repeat on every page.
 *
 * @var common\modules\sales\models\Quotation $model
 * @var common\modules\master\models\QuotationLayout $layout
 * @var callable $idDate  fn(string Y-m-d): "23 September 2026"
 */

$rupiah = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
$items = $model->quotationItems;
$recurring = array_values(array_filter($items, fn($i) => stripos((string) ($i->product->revenue_model ?? ''), 'Recurring') !== false));
$oneTime = array_values(array_filter($items, fn($i) => stripos((string) ($i->product->revenue_model ?? ''), 'Recurring') === false));
$recurringTotal = array_sum(array_map(fn($i) => (float) $i->total, $recurring));
$paymentMethod = $model->payment_method ?: $model->defaultPaymentMethod();
// signer: the quotation's own name/title (copied from Layout Quotation), else the Assigned Sales
$fallback = $model->account->assignedUser ?? $model->createdBy;
$signerName = $model->signer_name ?: ($fallback->fullname ?? '');
$signerTitle = $model->signer_name ? (string) $model->signer_title : ($fallback->job_title ?? '');
$terms = QuotationLayout::lines($model->terms_text);
$notes = QuotationLayout::lines($model->installation_notes);
$paragraphs = fn($text) => implode('', array_map(
    fn($p) => '<p class="para">' . nl2br(Html::encode(trim($p))) . '</p>',
    array_filter(preg_split('/\R\s*\R/', (string) $text), fn($p) => trim($p) !== '')
));
?>
<style>
    body { font-family: dejavusans, sans-serif; font-size: 10.5pt; color: #111; line-height: 1.35; }
    .title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 11.5pt; margin: 0; }
    .number { text-align: center; margin: 2px 0 18px 0; }
    .date { text-align: right; margin-bottom: 18px; }
    .para { margin: 0 0 8px 0; text-align: justify; }
    .indent { text-indent: 30px; }
    table.offer { width: 92%; margin: 10px auto 14px auto; border-collapse: collapse; }
    table.offer td { border: 0.6px solid #333; padding: 3px 6px; vertical-align: top; }
    table.offer td.k { width: 40%; }
    table.offer td.v { font-weight: normal; }
    table.offer tr.head td { font-weight: bold; }
    table.offer tr.sep td { border-left: none; border-right: none; padding: 2px; }
    ol, ul { margin: 2px 0 10px 0; padding-left: 26px; }
    li { margin-bottom: 2px; text-align: justify; }
    table.sign { width: 100%; margin-top: 18px; }
    table.sign td { width: 50%; text-align: center; vertical-align: top; }
    .signname { font-weight: bold; text-decoration: underline; }
</style>

<p class="title">PROPOSAL PENAWARAN HARGA</p>
<p class="number">Nomor: <?= Html::encode($model->quotation_number) ?></p>

<div class="date"><?= Html::encode($layout->city) ?>, <?= $idDate($model->quotation_date ?: date('Y-m-d')) ?></div>

<p class="para" style="margin-bottom:12px">
    Kepada Yth,<br>
    <?php if ($layout->recipient_title): ?><b><?= Html::encode($layout->recipient_title) ?></b><br><?php endif; ?>
    <b><?= Html::encode($model->account->name ?? '-') ?></b>
</p>

<?= $paragraphs($model->opening_text) ?>

<table class="offer">
    <?php foreach ($recurring as $n => $item): ?>
        <?php $uom = $item->product->uom->name ?? 'Unit'; ?>
        <?php if ($n > 0): ?><tr class="sep"><td colspan="2"></td></tr><?php endif; ?>
        <tr class="head"><td class="k" style="font-weight:normal">Nama Produk</td><td class="v"><b><?= Html::encode($item->product->name ?? '-') ?></b></td></tr>
        <?php if (!empty($item->product->package_info)): ?>
            <tr><td class="k">Keterangan Paket</td><td class="v"><?= Html::encode($item->product->package_info) ?></td></tr>
        <?php endif; ?>
        <tr><td class="k">Harga Paket</td><td class="v"><?= $rupiah($item->price) ?> / <?= Html::encode($uom) ?> (Exc PPN)</td></tr>
        <tr><td class="k">Jumlah Unit</td><td class="v"><?= number_format((int) $item->qty, 0, ',', '.') ?> <?= Html::encode($uom) ?></td></tr>
        <?php if ((float) $item->discount > 0): ?>
            <tr><td class="k">Diskon</td><td class="v"><?= $rupiah($item->discount) ?></td></tr>
        <?php endif; ?>
        <tr><td class="k">Total Harga</td><td class="v"><?= $rupiah($item->total) ?> (Exc PPN)</td></tr>
    <?php endforeach; ?>

    <?php if (count($recurring) > 1): ?>
        <tr class="sep"><td colspan="2"></td></tr>
        <tr><td class="k"><b>Total Harga Berlangganan</b></td><td class="v"><b><?= $rupiah($recurringTotal) ?> (Exc PPN)</b></td></tr>
    <?php endif; ?>

    <?php foreach ($oneTime as $item): ?>
        <tr><td class="k"><?= Html::encode($item->product->name ?? '-') ?></td><td class="v"><?= $rupiah($item->total) ?><?= (int) $item->qty > 1 ? ' (' . (int) $item->qty . ' x ' . $rupiah($item->price) . ')' : '' ?></td></tr>
    <?php endforeach; ?>

    <?php if ($model->contract_months): ?>
        <tr><td class="k">Durasi Kontrak</td><td class="v"><?= (int) $model->contract_months ?> Bulan</td></tr>
    <?php endif; ?>
    <?php if ($paymentMethod): ?>
        <tr><td class="k">Metode Pembayaran</td><td class="v"><?= Html::encode($paymentMethod) ?></td></tr>
    <?php endif; ?>
</table>

<?php if ($terms): ?>
    <p class="para" style="margin-bottom:2px">Syarat &amp; Ketentuan :</p>
    <ol>
        <?php foreach ($terms as $t): ?><li><?= Html::encode($t) ?></li><?php endforeach; ?>
    </ol>
<?php endif; ?>

<?php if ($notes): ?>
    <p class="para" style="margin-bottom:2px"><b>Notes Instalasi</b> :</p>
    <ul>
        <?php foreach ($notes as $t): ?><li><?= Html::encode($t) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<?= $paragraphs($model->closing_text) ?>

<div style="page-break-inside: avoid;">
    <p class="para" style="margin-top:14px"><?= Html::encode($layout->city) ?>, <?= $idDate($model->quotation_date ?: date('Y-m-d')) ?></p>
    <table class="sign">
        <tr>
            <td><?= Html::encode($layout->sign_left_label) ?></td>
            <td><?= Html::encode($layout->sign_right_label) ?></td>
        </tr>
        <tr><td style="height:70px"></td><td></td></tr>
        <tr>
            <td>
                <span class="signname"><?= Html::encode($signerName) ?></span><br>
                <?= Html::encode($signerTitle) ?>
            </td>
            <td><b><?= Html::encode($model->account->name ?? '') ?></b></td>
        </tr>
    </table>
</div>
