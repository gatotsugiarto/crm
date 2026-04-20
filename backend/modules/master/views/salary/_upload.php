<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Upload Salary Data';
?>

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">

                <!-- HEADER -->
                <div class="card-header bg-white border-0 text-center pt-4">
                    <div class="icon icon-primary mb-2">

                        <!-- CLOSE BUTTON -->
    <button type="button"
        class="close position-absolute"
        style="top:10px; right:12px;"
        data-dismiss="modal"
        aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
 
                        <i class="fa fa-cloud-upload-alt fa-2x text-info"></i>
                    </div>
                    <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
                    <p class="text-muted small">
                        Import salary data from Input (xls)
                    </p>
                </div>

                <!-- BODY -->
                <div class="card-body px-4 pb-4">

                    <?php $form = ActiveForm::begin([
                        'id' => 'salary-upload-form',
                        'action' => ['salary/upload'],
                        'enableAjaxValidation' => false,
                        'options' => [
                            'enctype' => 'multipart/form-data',
                            'data-pjax' => 0,
                        ],
                    ]); ?>

                    <div class="form-group">
                        <?= $form->field($model, 'file')
                            ->fileInput(['class' => 'form-control'])
                            ->label('Upload Excel File') ?>
                        <small class="form-text text-muted">
                            Supported format: <strong>.xls, .xlsx</strong>
                        </small>
                    </div>

                    <div class="text-center mt-4">
                        <?= Html::submitButton(
    '<i class="fa fa-upload"></i> Import',
    [
        'class' => 'btn btn-info btn-round btn-sm px-4',
        'type'  => 'submit',
    ]
) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>

                <!-- FOOTER -->
                <div class="card-footer bg-white text-center border-0 pb-4">
                    <small class="text-muted">
                        Make sure the payroll period and employee data are correct before uploading.
                    </small>
                </div>

            </div>

        </div>
    </div>

</div>
