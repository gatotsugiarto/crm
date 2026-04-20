<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\InvoiceSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="invoice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="d-flex align-items-center" style="gap: 8px;">

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'invoice_number') ?>

    <?= $form->field($model, 'account_id') ?>

    <?= $form->field($model, 'sales_order_id') ?>

    <?= $form->field($model, 'invoice_date') ?>

    <?php // echo $form->field($model, 'due_date') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'status_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

        <?=  Html::submitButton('<i class="fa fa-search"></i> Search', [
            'class' => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm',
        ]) ?>

        <?=  Html::a('<i class="fa fa-sync"></i> Reset', ['index'], [
            'class' => 'btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-sm',
        ]) ?>

    </div>
    <?php ActiveForm::end(); ?>

</div>
