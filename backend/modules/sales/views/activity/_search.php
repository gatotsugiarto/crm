<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\ActivitySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="activity-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>
    
    <div class="d-flex align-items-center" style="gap: 8px;">

        <?= $form->field($model, 'subject', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->textInput([
            'placeholder' => 'Subject',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 220px;',
        ]) ?>

        <?= $form->field($model, 'outcome', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->textInput([
            'placeholder' => 'Outcome',
            'class' => 'form-control form-control-sm',
            'style' => 'width: 220px;',
        ]) ?>

        <div style="width: 200px;">
        <?= $form->field($model, 'activity_type', [
            'options' => ['class' => 'mb-0'],
            'template' => '{input}'
        ])->widget(Select2::classname(), [
            'data' => [ 
                'Call' => 'Call', 
                'Meeting' => 'Meeting', 
                'Email' => 'Email', 
                'Task' => 'Task', 
                'Note' => 'Note', 
            ],
            'options' => [
                'placeholder' => 'Type',
                'multiple' => false,
                'class' => 'form-control form-control-sm',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new \yii\web\JsExpression('function (m) { return m; }'),
            ],
        ])->label(false) ?>
        </div>



        <?php // echo $form->field($model, 'account_id') ?>

        <?php // echo $form->field($model, 'contact_id') ?>

        <?php // echo $form->field($model, 'opportunity_id') ?>

        <?php // echo $form->field($model, 'reference_type') ?>

        <?php // echo $form->field($model, 'reference_id') ?>

        <?php // echo $form->field($model, 'assigned_to') ?>

        <?php // echo $form->field($model, 'priority') ?>

        <?php // echo $form->field($model, 'activity_date') ?>

        <?php // echo $form->field($model, 'due_date') ?>

        <?php // echo $form->field($model, 'reminder_at') ?>

        <?php // echo $form->field($model, 'is_completed') ?>

        <?php // echo $form->field($model, 'completed_at') ?>

        <?php // echo $form->field($model, 'description') ?>

        <?php // echo $form->field($model, 'outcome') ?>

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
