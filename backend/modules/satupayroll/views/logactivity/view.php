<?php
use yii\helpers\Html;
?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <div class="mb-3">
            <div class="mb-0 text-primary fw-bold text-primary fw-bold page-title"><i class="fa fa-user-circle"></i>&nbsp;&nbsp;&nbsp;Detail Log Activity</div>
            <small class="text-muted">This section provides detailed information about the log activity, including action, model/menu, before and after data, remarks, action by, and creation history.</small>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Action</span><br>
                <span class='badge badge-warning'><?= Html::encode($model->controller_action) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Model/ Menu</span><br>
                <span class='badge badge-danger'><?= Html::encode($model->model_name) ?></span>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Action By</span><br>
                <?= Html::encode($model->user->fullname) ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Datetime</span><br>
                <?= Html::encode($model->created_at) ?>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Request URL</span><br>
                <?= Html::encode($model->request_url) ?>
            </div>
            <div class="col-md-6">
                <?php
                $addLabel = '';
                $fullname = '';
                if($model->employee){
                    $addLabel = '/ Employee';
                    $fullname = '/ '. $model->employee->fullname;
                }
                ?>
                <span class="text-secondary small">Record ID<?=$addLabel?></span><br>
                <span class='badge badge-success'><?= Html::encode($model->record_id) ?><?=$fullname?></span>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">IP Address</span><br>
                <?= Html::encode($model->ip_address) ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">User Agent</span><br>
                <small><?= Html::encode($model->user_agent) ?></small>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <?= Html::encode($model->status) ?>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Remarks</span><br>
                <?= Html::encode($model->remarks) ?>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <span class="text-secondary small">Before Data</span><br>
                <pre class="bg-dark text-white p-3 rounded" style="font-size: 13px; overflow-x: auto;">
                    <?= $model->before_data ?>
                </pre>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">After Data</span><br>
                <pre class="bg-dark text-white p-3 rounded" style="font-size: 13px; overflow-x: auto;">
                    <?= $model->after_data ?>
                </pre>
            </div>
        </div>
    </div>
</div>
<div class="text-end mt-3">
    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary mr-2 px-4',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>
</div>
    