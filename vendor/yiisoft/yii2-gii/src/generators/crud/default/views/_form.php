<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$modelClass = StringHelper::basename($generator->modelClass);
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New <?= ucfirst($modelClass) ?>' : 'Edit <?= ucfirst($modelClass) ?>';
$icon = $isNew ? 'fa-user-plus' : 'fa-edit';

/** @var yii\web\View $this */
/** @var <?= ltrim($generator->modelClass, '\\') ?> $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="modal-header bg-default text-white rounded-top-4">
    <div>
        <h5 class="text-primary fw-bold page-title mb-1">
            <i class="fa <?php echo "<?=" ?> $icon ?> mr-2"></i> <?php echo "<?=" ?> $title ?>
        </h5>
        <small class="text-muted">
            <?php echo "<?=" ?> $isNew
                ? 'Please fill in the form below to register a new <?= strtolower($modelClass)?>.'
                : 'Update <?= strtolower($modelClass)?> information below.' ?>
        </small>
    </div>
</div>

<?php echo "<?php" ?> $form = ActiveForm::begin([
    'id' => '<?= strtolower($modelClass)?>-form',
    'enableAjaxValidation' => false,
    // 'validationUrl' => ['<?= strtolower($modelClass)?>/validate'],
    'action' => $isNew ? ['<?= strtolower($modelClass)?>/create'] : ['<?= strtolower($modelClass)?>/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">
        
        <?php echo "<?=" ?> Html::hiddenInput('form_token', $formToken) ?>
<?php
$field = ['id','status_id','created_at','created_by','updated_at','updated_by'];

$fields = [];
foreach ($generator->getColumnNames() as $attribute) {
    if (!in_array($attribute, $field) && in_array($attribute, $safeAttributes)) {
        $fields[] = $attribute;
    }
}

$total = count($fields);
?>

<?php for ($i = 0; $i < $total; $i += 2): ?>
        <div class="row">
            <div class="col-md-6">
                <?php echo "<?=" ?> <?= $generator->generateActiveField("$fields[$i]") ?> ?>
            </div>

        <?php if (isset($fields[$i+1])): ?>
    <div class="col-md-6">
                <?php $j = $i+1 ?>
<?php echo "<?=" ?> <?= $generator->generateActiveField("$fields[$j]") ?> ?>
            </div>
        <?php endif; ?>
</div>

<?php endfor; ?>
        <div class="row">
            <div class="col-md-6">
                <?php echo "<?php" ?> if(!$isNew): ?>
                    <?php echo "<?=" ?> $form->field($model, 'status_id')->widget(Select2::classname(), [
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
                <?php echo "<?php" ?> else: ?>
                    <?php echo "<?=" ?> $form->field($model, 'status_id')->hiddenInput()->label(false) ?>
                <?php echo "<?php" ?> endif; ?>
            </div>
            <div class="col-md-6"></div>
        </div>

    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <!-- LEFT: Back / Close -->
    <div>
        <?php echo "<?php" ?>  if (Yii::$app->request->isAjax): ?>

            <?php echo "<?=" ?> Html::button('<i class="fa fa-times"></i> Close', [
                'class' => 'btn btn-outline-secondary px-4',
                'data-dismiss' => 'modal',
                'style' => 'min-width:140px;',
            ]) ?>

        <?php echo "<?php" ?> else: ?>

            <?php echo "<?=" ?> Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
                'class' => 'btn btn-outline-secondary px-4',
                'style' => 'min-width:140px;',
            ]) ?>

        <?php echo "<?php" ?> endif; ?>
    </div>

    <!-- RIGHT: Submit -->
    <div>
        <?php echo "<?=" ?> Html::submitButton('<i class="fa fa-save"></i> Save', [
            'class' => 'btn btn-primary px-4',
            'style' => 'min-width:140px;',
        ]) ?>
    </div>

</div>


<?php echo "<?php" ?> ActiveForm::end(); ?>
