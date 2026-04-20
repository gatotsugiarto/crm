<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\file\FileInput;

$isNew = $model->isNewRecord;
$title = $isNew ? 'Create New Company' : 'Edit Company';
$icon = $isNew ? 'fa-plus' : 'fa-edit';
?>

<div class="modal-header bg-white border-0 pt-4 pb-3">
    <div>
        <h5 class="text-primary fw-bold mb-1">
            <i class="fa <?= $icon ?> mr-2"></i> <?= $title ?>
        </h5>
        <p class="text-muted pb-2 mb-0 border-bottom">
            <?= $isNew 
                ? 'Please fill in the form below to register a new company.' 
                : 'Update company information below.' ?>
        </p>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'company-form',
    'enableAjaxValidation' => true,
    'validationUrl' => ['company/validate'],
    'action' => $isNew 
        ? ['company/create'] 
        : ['company/update', 'id' => $model->id],
    'options' => ['data-pjax' => 0],
]); ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="modal-body px-4 pb-4">

        <?= Html::hiddenInput('form_token', $formToken) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'code')->textInput([
                    'placeholder' => 'Enter Company Code',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'company')->textInput([
                    'placeholder' => 'Enter Company Name',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'address')->textInput([
                    'placeholder' => 'Enter Company Address',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'npwp')->textInput([
                    'placeholder' => 'Enter NPWP',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'description')->textInput([
                    'placeholder' => 'Enter Company Description',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'nama_pejabat')->textInput([
                    'placeholder' => 'Enter Nama Pejabat - Formulir 1721-A1',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'sign_name')->textInput([
                    'placeholder' => 'Enter Sign Name - Formulir 1721-A1',
                    'class' => 'form-control form-control-custom'
                ]) ?>
            </div>

            <?php
            if($model->isNewRecord){
                $allimage = array();
                $return_json = array();
                $initialPreview = null;
            }else{
                $allimage = array();
                $return_json = array();
                if($model->sign_image){
                    $photos=explode('**',trim($model->sign_image));
                    //$image=$model->attachment; // Single
                    foreach($photos as $image){
                        $initialPreview[] = Yii::$app->params['uploadDomain'] . Yii::$app->params['uploadAttachment'] . $image;
                        $allimage[] = Yii::$app->params['uploadAttachment'] . $image;
                        $return_json[] = [
                            'key'=> array($model->id.'###'.$image)
                        ];
                    }
                }else{
                    $initialPreview = null;
                }
            }
            ?>
            <div class="col-md-6">
                <?= $form->field($model, 'file')->widget(FileInput::classname(), [
                    'options'=>[
                        'multiple' => true,
                        'layout' => 'horizontal',
                        'accept' => 'image/*'
                    ],
                    'pluginOptions'=>[
                        'allowedFileExtensions'=>['jpg','jpeg','gif','png'],
                        'showRemove'=> false,
                        'showUpload' => false,
                        'showCancel' => false,
                        'maxFileSize' => 700,
                        'initialPreview'=> $allimage,
                        'initialPreviewAsData'=>true,
                        'initialPreviewConfig'=> $return_json,
                        'overwriteInitial' => false,
                        'deleteUrl' => Url::to(['/master/company/deleteattachment']),
                    ]
                ]);?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6"></div>

            <div class="col-md-6">
                <?php if ($isNew): ?>
                    <?= $form->field($model, 'status_id')->hiddenInput(['value' => 1])->label(false) ?>
                <?php else: ?>
                    <?= $form->field($model, 'status_id')->widget(Select2::class, [
                        'data' => common\modules\master\models\StatusActive::dropdown(),
                        'options' => [
                            'placeholder' => 'Select status...',
                            'class' => 'select2-custom'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'dropdownParent' => new \yii\web\JsExpression('$("#appModal")')
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end mt-4">
    <?= Html::button('<i class="fa fa-times"></i> Cancel', [
        'class' => 'btn btn-outline-secondary mr-2 px-4',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>
    
    <?= Html::submitButton('<i class="fa fa-save"></i> Save', [
        'class' => 'btn btn-primary px-4',
        'style' => 'min-width:140px;',
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
    
