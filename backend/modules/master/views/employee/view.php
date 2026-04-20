<?php
use yii\helpers\Html;
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;<?=$model->fullname?> [<?=$model->e_number ?>] 
    </h5>
    <p class="text-muted small mb-0">
        <small><?=$model->company ?> - <?=$model->region ?> - <?=$model->site_office ?><br />
        <?=$model->department ?> - <?=$model->division ?></small>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Join Date</span><br>
                <span class="fw-semibold"><small><?=$model->join_date ?></small></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Email</span><br>
                <span class="fw-semibold"><small><?= Html::encode($model->email) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Position/ Grade</span><br>
                <span><small><?=$model->level_jabatan ?>, <?=$model->jabatan ?>/ <?=$model->grade ?></small></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Marital/ Family Status/ PTKP</span><br>
                <span><small><?=$model->marital_status ?>/ <?=$model->family_status ?>/ <?=$model->ptkp ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Bank - Bank No.</span><br>
                <span><small><?=$model->bank ?> - <?=$model->bank_no ?></small></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Cost Center</span><br>
                <span><small><?=$model->costcenter->code ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <?php
            if($model->join_prorate){
                $join_prorate = $model->join_prorate;
            }else{
                $join_prorate = '-';
            }
            ?>
            <div class="col-md-6">
                <span class="text-secondary small">Join Prorate</span><br>
                <span><small><?=$join_prorate ?></small></span>
            </div>
            <?php
            if($model->resign_date){
                $resign_prorate = $model->resign_prorate;
                $resign_date = $model->resign_date;
            }else{
                $resign_prorate = '-';
                $resign_date = '-';
            }
            ?>
            <div class="col-md-6">
                <span class="text-secondary small">Resign Date/ Prorate</span><br>
                <span><small><?=$resign_date ?>/ <?=$resign_prorate ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">NPWP/ JKK</span><br>
                <span><small><?=$model->npwp_id ?>/ <?=$model->jkk ?></small></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Employee Status</span><br>
                <small><?=$model->employee_status ?></small>&nbsp;
                <?php
                $statusClass = [
                    1 => 'badge badge-primary px-2 py-1',
                    2 => 'badge badge-secondary px-2 py-1',
                ];
                echo Html::tag('span', $model->status->status, [
                    'class' => $statusClass[$model->status_id] ?? 'badge bg-secondary px-2 py-1'
                ]);
                ?>
            </div>
        </div>

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
