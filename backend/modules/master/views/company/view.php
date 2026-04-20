<?php
use yii\helpers\Html;
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;Detail Company
    </h5>
    <p class="text-muted small mb-0">
        This section provides detailed information about the company, including its unique code, registered name, descriptive profile, current status, and a log of recent updates.
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">NPWP</span><br>
                <span class="fw-semibold"><?= Html::encode($model->npwp) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Company Name</span><br>
                <span class="fw-semibold"><?= Html::encode($model->company) ?> [<?= Html::encode($model->code) ?>]</span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                
                <?php
                $statusClass = [
                    1 => 'badge badge-primary px-2 py-1',
                    2 => 'badge badge-secondary px-2 py-1',
                ];
                echo Html::tag('span', $model->status->status_active, [
                    'class' => $statusClass[$model->status_id] ?? 'badge bg-secondary px-2 py-1'
                ]);
                ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Nama Pejabat - Formulir 1721-A1</span><br>
                <span class="fw-semibold"><?= Html::encode($model->nama_pejabat) ?></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Sign Name - Formulir 1721-A1</span><br>
                <span class="fw-semibold"><?= Html::encode($model->sign_name) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Sign - Image - Formulir 1721-A1</span><br>
                <span class="fw-semibold">
                    <?php
                    if($model->sign_image){   
                        $photos=explode('**',trim($model->sign_image));
                        foreach($photos as $image){
                            if($image) {
                                echo Html::img(Yii::$app->params['uploadAttachment'] . $image, [
                                    'alt'   => 'Company Logo',
                                    'class' => 'img-fluid',
                                    'width' => 120,
                                ]);
                            }else{
                                $images = "";
                            }
                        }
                    }
                    ?>
                </span>
            </div>
        </div>

        <!-- <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Description</span><br>
                <span><?= Html::encode($model->description ?: '-') ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Address</span><br>
                <span><small><?= Html::encode($model->address) ?></small></span>
            </div>
        </div> -->

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Address</span><br>
                <span><small><?= Html::encode($model->address) ?></small></span>
            </div>
        </div>

        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Created by:
                <strong><?= Html::encode($model->createdBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->created_at) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="fa fa-edit"></i> Updated by:
                <strong><?= Html::encode($model->updatedBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= Html::encode($model->updated_at) ?></small>
            </div>
        </div>

    </div>
</div>

<div class="text-end mt-3">
    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>
</div>
