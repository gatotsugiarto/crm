<?php

use yii\helpers\Html;
use common\modules\sales\models\Lead;

/**
 * Sales Order form body (2 pages), rendered by mPDF in SalesorderController::actionPdf.
 * Logo, "SALES ORDER" and the SO number are the page header; the consent line and
 * "Lembar" distribution are the footer. Blank lines are left where the CRM has no
 * data (RT/RW, Kelurahan, Kecamatan, fax, customer signature) so they can be filled
 * in by hand.
 *
 * @var common\modules\sales\models\SalesOrder $model
 * @var common\modules\master\models\QuotationLayout $layout
 * @var callable $idDate  fn(?string Y-m-d): "18 September 2026" or ''
 */

$e = fn($v) => Html::encode((string) $v);
$rp = fn($v) => 'Rp. ' . number_format((float) $v, 0, ',', '.');
$box = fn($checked) => '<span style="font-family:dejavusans;font-size:11pt">' . ($checked ? '&#9745;' : '&#9744;') . '</span>';
$account = $model->account;
$items = $model->salesOrderItems;

// --- service: type boxes, bandwidth, prices by revenue model -----------------------
$types = array_map(fn($i) => (string) ($i->product->service_type ?? ''), $items);
$isFtthd = in_array('FTTHD', $types, true);
$isMetro = in_array('Metro-E', $types, true);
$isOther = (bool) array_filter($types, fn($t) => $t !== 'FTTHD' && $t !== 'Metro-E');
$bandwidth = implode(', ', array_unique(array_filter(array_map(fn($i) => (string) ($i->product->bandwidth ?? ''), $items))));
$model_ = fn($i) => (string) ($i->product->revenue_model ?? '');
$monthly = array_sum(array_map(fn($i) => (float) $i->total, array_filter($items, fn($i) => stripos($model_($i), 'Bulanan') !== false)));
$yearly = array_sum(array_map(fn($i) => (float) $i->total, array_filter($items, fn($i) => stripos($model_($i), 'Tahun') !== false)));
$oneTime = array_sum(array_map(fn($i) => (float) $i->total, array_filter($items, fn($i) => stripos($model_($i), 'Bulanan') === false && stripos($model_($i), 'Tahun') === false)));
$total = array_sum(array_map(fn($i) => (float) $i->total, $items));
$months = (int) ($model->quotation->contract_months ?? 0);
$contractLabel = $months ? ($months % 12 === 0 ? 'Kontrak ' . ($months / 12) . ' Tahun / ' . ($months / 12) . ' Year Contract' : "Kontrak $months Bulan / $months Month Contract") : '';

// --- installation / billing ------------------------------------------------------
$instAddr = $model->effectiveAddress('installation');
$instContact = $model->effectiveContact('installation');
$billAddr = $model->effectiveAddress('billing');
$billContact = $model->effectiveContact('billing');

// --- marketing: lead source of the lead this account was converted from ----------
$source = (string) (Lead::find()->select('lead_source')->where(['converted_account_id' => $model->account_id])->scalar() ?: '');
$src = strtolower($source);
$mk = [
    'web' => $source !== '' && str_contains($src, 'web'),
    'ref' => $source !== '' && (str_contains($src, 'referr') || str_contains($src, 'referen')),
    'search' => $source !== '' && (str_contains($src, 'google') || str_contains($src, 'search') || str_contains($src, 'facebook')),
];
$mk['other'] = $source !== '' && !in_array(true, $mk, true);

// --- internal use ----------------------------------------------------------------
$am = $account->assignedUser->fullname ?? '';
$secHead = $account->team->user->fullname ?? '';
$deptHead = $model->confirmedBy->fullname ?? '';
$signDate = $model->confirmed_at ? $idDate(substr($model->confirmed_at, 0, 10)) : '';

/** one "Label / English : value" row with an underlined value */
$row = fn($label, $value, $en = '') => '<tr><td class="lbl">' . $label . ($en ? ' / <i>' . $en . '</i>' : '') . '</td><td class="colon">:</td><td class="val">' . $value . '</td></tr>';
/** address block: street, RT/RW/Kelurahan/Kecamatan line (blank), city + postal code */
$addressRows = function ($label, $en, $addr, $prefix = '') use ($e, $row) {
    $street = $addr ? $e($addr->address) : '';
    $html = $row($label, ($prefix !== '' ? $e($prefix) . '<br>' : '') . $street, $en);
    $html .= '<tr><td></td><td></td><td class="val small">RT : ________ &nbsp; RW : ________ &nbsp; Kelurahan : ______________________ &nbsp; Kecamatan : ______________________</td></tr>';
    $html .= '<tr><td class="lbl">Kota / <i>City</i></td><td class="colon">:</td><td class="val">'
        . '<table class="inner"><tr><td style="width:55%">' . $e($addr->city->name ?? '') . '</td>'
        . '<td class="lbl" style="width:24%;white-space:nowrap">Kode Pos / <i>Pos Code</i> :</td><td style="border-bottom:0.5px solid #555">' . $e($addr->postalCode->code ?? '') . '</td></tr></table></td></tr>';
    return $html;
};
$contactRows = function ($contact) use ($e) {
    // flat 5 columns (nested tables get shrunk by mPDF)
    $u = 'border-bottom:0.5px solid #555;font-weight:bold';
    return '<table class="f"><tr><td style="width:31%">Nama Yang Dapat Dihubungi / <i>Contact</i></td><td style="width:2%">:</td>'
        . '<td colspan="3" style="' . $u . '">' . $e($contact->fullname ?? '') . '</td></tr>'
        . '<tr><td>Telepon Kantor / <i>Office Phone</i></td><td>:</td><td style="width:22%;' . $u . '">' . $e($contact->phone ?? '') . '</td>'
        . '<td style="width:13%;text-align:right">No Ponsel / <i>Mobile</i> :</td><td style="' . $u . '">' . $e($contact->mobile ?? '') . '</td></tr>'
        . '<tr><td>No. Fax / <i>Fax No</i></td><td>:</td><td style="' . $u . '">&nbsp;</td>'
        . '<td style="text-align:right">Email :</td><td style="' . $u . '">' . $e($contact->email ?? '') . '</td></tr></table>';
};
?>
<style>
    body { font-family: dejavusans, sans-serif; font-size: 8.4pt; color: #111; }
    .intro { margin: 0 0 3mm 0; }
    .sect { text-align: right; font-weight: bold; font-size: 9.5pt; border-bottom: 1px solid #333; padding-bottom: 0.8mm; margin: 3mm 0 1.2mm 0; }
    .sub { font-weight: bold; margin: 1.5mm 0 1mm 0; }
    table.f { width: 100%; border-collapse: collapse; }
    table.f td { padding: 0.7mm 1mm; vertical-align: top; }
    td.lbl { width: 31%; }
    td.colon { width: 2%; }
    td.val { border-bottom: 0.5px solid #555; font-weight: bold; }
    td.val.small { font-weight: normal; font-size: 7.4pt; color: #444; }
    table.inner { width: 100%; border-collapse: collapse; }
    table.inner td { padding: 0 1mm 0 0; font-weight: bold; }
    table.inner td.lbl { font-weight: normal; width: auto; }
    table.price { width: 92%; margin: 2mm auto; border-collapse: collapse; }
    table.price td { border: 0.6px solid #333; padding: 1.2mm 2mm; vertical-align: top; }
    .note { font-size: 7.2pt; }
    .lines td { border-bottom: 0.5px solid #555; padding: 1.1mm 1mm; font-size: 9pt; }
    table.sign { width: 88%; margin: 2mm auto; border-collapse: collapse; }
    table.sign td { border: 0.6px solid #333; padding: 1.2mm 2mm; text-align: center; }
    .muted { color: #555; }
</style>

<!-- ============================ PAGE 1 ============================ -->
<p class="intro">Mohon untuk dapat memberikan informasi detail agar kami dapat menyediakan layanan dengan cepat<br>
    <i>Please provide detailed information so we can provision the service faster</i></p>

<div class="sect">CUSTOMER INFORMATION</div>
<div class="sub">DATA PERUSAHAAN / <i>COMPANY DETAILS</i> <span style="font-weight:normal">( SESUAI NPWP PERUSAHAAN/AS PER COMPANY REGISTRATION )</span></div>
<table class="f">
    <?= $row('Nama Perusahaan', $e($account->name ?? ''), 'Company Name') ?>
    <?= $row('NPWP', $e($account->tax_number ?? '')) ?>
    <?= $row('Alamat / <i>Address</i> ( Sesuai NPWP )', $e($account->address ?? '')) ?>
    <tr><td></td><td></td><td class="val small">RT : ________ &nbsp; RW : ________ &nbsp; Kelurahan : ______________________ &nbsp; Kecamatan : ______________________</td></tr>
    <tr><td class="lbl">Kota / <i>City</i></td><td class="colon">:</td><td class="val">
        <table class="inner"><tr><td style="width:55%"><?= $e($account->city->name ?? '') ?></td>
            <td class="lbl" style="width:24%;white-space:nowrap">Kode Pos / <i>Pos Code</i> :</td><td style="border-bottom:0.5px solid #555"><?= $e($account->postalCode->code ?? '') ?></td></tr></table></td></tr>
    <tr><td class="lbl">Telepon / <i>Telephone</i></td><td class="colon">:</td><td class="val">
        <table class="inner"><tr><td style="width:45%"><?= $e($account->phone ?? '') ?></td>
            <td class="lbl" style="width:20%;white-space:nowrap">No. Fax / <i>Fax No</i> :</td><td style="border-bottom:0.5px solid #555">&nbsp;</td></tr></table></td></tr>
</table>

<div class="sect">SERVICE INFORMATION</div>
<table class="f">
    <tr><td class="lbl">Jenis Layanan / <i>Service Type</i></td><td class="colon">:</td>
        <td><?= $box($isFtthd) ?> FTTHD &nbsp;&nbsp;&nbsp;&nbsp; <?= $box($isMetro) ?> Metro - E &nbsp;&nbsp;&nbsp;&nbsp; <?= $box($isOther) ?> Lain - Lain / <i>Other</i></td></tr>
    <?= $row('Kapasitas / <i>Bandwidth</i>', $e($bandwidth)) ?>
</table>

<table class="price">
    <tr>
        <td style="width:36%">Harga / <i>Price</i> (IDR)</td>
        <td style="width:34%">Biaya Instalasi / <i>Installation Fee</i> (IDR) - 1 time fee</td>
        <td><?= $box(true) ?> Tidak termasuk PPN / <i>Exclude Tax</i></td>
    </tr>
    <tr>
        <td><b><?= $monthly ? $rp($monthly) : '' ?></b> Per Bulan - <i>month</i><br>
            <b><?= $yearly ? $rp($yearly) : '' ?></b> Per Tahun - <i>Year</i></td>
        <td><b><?= $oneTime ? $rp($oneTime) : '' ?></b></td>
        <td><?= $contractLabel ? $box(true) . ' ' . $e($contractLabel) : '' ?></td>
    </tr>
</table>

<table class="f">
    <tr><td class="lbl">Jenis Layanan Lain / <i>Other Service Type</i></td><td class="colon">:</td><td></td></tr>
</table>
<table class="f lines">
    <?php foreach ($items as $item): ?>
        <?php $uom = $item->product->uom->name ?? 'Unit'; ?>
        <tr><td><b><?= $e($item->product->name ?? '-') ?></b>
            <?= number_format((int) $item->qty, 0, ',', '.') ?> <?= $e($uom) ?> x IDR <?= number_format((float) $item->price, 0, ',', '.') ?>
            <?= (float) $item->discount > 0 ? ' - diskon ' . $rp($item->discount) : '' ?>
            = <?= $rp($item->total) ?> (Exc PPN)</td></tr>
    <?php endforeach; ?>
    <tr><td><b>Total <?= $rp($total) ?></b> (Exc PPN)</td></tr>
    <tr><td>
        <?php if ($model->trial_start || $model->trial_end): ?>
            <b>Periode trial <?= $idDate($model->trial_start) ?> s/d <?= $idDate($model->trial_end) ?></b> &nbsp;&nbsp;&nbsp;
        <?php endif; ?>
        <?php if ($model->contract_start || $model->contract_end): ?>
            <b>Periode Kontrak <?= $idDate($model->contract_start) ?> s/d <?= $idDate($model->contract_end) ?></b>
        <?php endif; ?>&nbsp;
    </td></tr>
</table>

<table class="f note" style="margin-top:1.5mm">
    <tr>
        <td style="width:50%"><b>Catatan Layanan FTTHD :</b><br>
            Free 1 IP Public DHCP dan Tidak bisa menambah IP Public<br>
            Pelanggan menyediakan Router &amp; jaringan LAN sendiri termasuk konfigurasinya<br>
            Tidak mendapat fasilitas MRTG</td>
        <td><b>Catatan Layanan Metro E :</b><br>
            Free 1 IP Public<br>
            Dikenakan biaya Rp 150,000/ bulan per IP utk setiap penambahan IP<br>
            Free MRTG</td>
    </tr>
</table>

<div class="sect">INSTALLATION INFORMATION</div>
<div class="sub">DATA PEMASANGAN / <i>INSTALLATION DETAIL</i></div>
<table class="f">
    <?= $addressRows('Alamat Pemasangan', 'Installation Address', $instAddr) ?>
</table>
<p style="margin:1.5mm 0 1mm 0">Untuk pertanyaan teknis atau pemasangan, dapat menghubungi / <i>For technical inquiries or installation, please contact</i></p>
<?= $contactRows($instContact) ?>

<!-- ============================ PAGE 2 ============================ -->
<pagebreak />

<div class="sect">BILLING INFORMATION</div>
<table class="f">
    <?= $addressRows('Alamat', 'Address', $billAddr) ?>
</table>
<p style="margin:1.5mm 0 1mm 0">Untuk informasi penagihan, mohon dapat menghubungi / <i>For billing information, please contact</i></p>
<?= $contactRows($billContact) ?>

<div class="sect">MARKETING INFORMATION</div>
<p style="margin:0 0 1mm 0">Bagaimana anda mengetahui <?= $e($layout->company_name) ?> / <i>How do you know us?</i></p>
<table class="f">
    <tr>
        <td style="width:34%"><?= $box(false) ?> Surat Kabar / <i>Newspaper</i></td>
        <td style="width:33%"><?= $box(false) ?> Majalah / <i>Magazine</i></td>
        <td><?= $box(false) ?> Radio</td>
    </tr>
    <tr>
        <td><?= $box($mk['search']) ?> Search Engine : <i>Yahoo/Google/Facebook</i></td>
        <td><?= $box($mk['web']) ?> Website</td>
        <td><?= $box($mk['ref']) ?> Referensi dari teman / keluarga / <i>Referenced by friend / family</i></td>
    </tr>
    <tr><td colspan="3"><?= $box($mk['other']) ?> Lain-Lain / <i>Others</i> : <b><?= $mk['other'] ? $e($source) : '' ?></b></td></tr>
</table>

<div class="sect">AUTHORIZATION</div>
<div class="note" style="font-size:7.2pt">
    &bull; Kami menyatakan bahwa informasi di atas adalah benar / <i>We hereby confirm that the information above is true</i><br>
    &bull; Dengan ini kami menyatakan konfirmasi dan setuju dengan isi halaman depan serta Syarat &amp; Ketentuan Berlangganan yang berlaku / <i>We agree &amp; confirm with the information on the front page as well as the Terms and Conditions of the service subscription</i><br>
    &bull; <?= $e($layout->company_name) ?> berhak untuk menolak Sales Order ini tanpa penjelasan / <i>We have the right to refuse this Sales Order without any explanation</i><br>
    &bull; Syarat &amp; Ketentuan dapat berubah sewaktu-waktu tanpa pemberitahuan lebih dahulu / <i>Terms and Conditions may change at any time without prior notice</i>
</div>
<p style="margin:2mm 0 1mm 0"><b>Mohon untuk dapat melampirkan copy / Please attach the following copies</b></p>
<table class="f">
    <tr><td style="width:20%"><?= $box(false) ?> NPWP</td><td style="width:40%"><?= $box(false) ?> KTP / Paspor sesuai Akte Perusahaan</td><td><?= $box(false) ?> Akte Perusahaan</td></tr>
    <tr><td colspan="3"><?= $box(false) ?> Surat Keterangan Domisili (SKD), ketika alamat pemasangan berbeda</td></tr>
    <tr><td colspan="3"><?= $box(false) ?> Akte Perubahan Kepengurusan Terakhir (Jika ada perubahan)</td></tr>
</table>
<p class="note muted" style="margin:1mm 0">KTP, NPWP, Akte Pendirian Perusahaan pada hari di tanda tangani Formulir Sales Order. Sisa dokumen diatas harus di lengkapi paling lambat H+2</p>
<p style="margin:2mm 0 0 0"><b>Signature &amp; Company Stamp</b></p>
<p class="muted" style="margin:5mm 0 0 0"><i>Materai</i></p>
<table class="f">
    <tr><td class="lbl" style="width:15%">Nama / <i>Nama</i></td><td class="colon">:</td><td class="val">&nbsp;</td></tr>
    <tr><td class="lbl">Tanggal / <i>Date</i></td><td class="colon">:</td><td class="val">&nbsp;</td></tr>
</table>

<div class="sect">INTERNAL USE</div>
<table class="sign">
    <tr><td style="width:33%"><b>Account Manager</b></td><td style="width:33%"><b>Sec. Head</b></td><td><b>Dept. Head</b></td></tr>
    <tr><td style="height:13mm"></td><td></td><td></td></tr>
    <tr>
        <td style="text-align:left">Name : <b><?= $e($am) ?></b></td>
        <td style="text-align:left">Name : <b><?= $e($secHead) ?></b></td>
        <td style="text-align:left">Name : <b><?= $e($deptHead) ?></b></td>
    </tr>
    <tr>
        <td style="text-align:left">Date : <b><?= $e($signDate) ?></b></td>
        <td style="text-align:left">Date : <b><?= $e($signDate) ?></b></td>
        <td style="text-align:left">Date : <b><?= $e($signDate) ?></b></td>
    </tr>
</table>
<table class="f" style="margin-top:2mm">
    <?= $row('Tanggal RFS', $e($idDate($model->rfs_date)), 'RFS Date') ?>
    <?= $row('No PKS', $e($model->pks_number), 'Contract Number') ?>
    <tr><td class="lbl">Periode Kontrak / <i>Contract Period</i></td><td class="colon">:</td><td class="val">
        <table class="inner"><tr><td class="lbl" style="width:10%">From :</td><td style="width:40%"><?= $e($idDate($model->contract_start)) ?></td>
            <td class="lbl" style="width:6%">To :</td><td><?= $e($idDate($model->contract_end)) ?></td></tr></table></td></tr>
</table>
