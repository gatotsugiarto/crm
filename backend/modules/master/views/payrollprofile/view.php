<?php
use yii\helpers\Html;

$this->title = "Detail Payroll Profile";
$sub_title = "View detailed information and related records.";
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?=$this->title ?>
    </h5>
    <p class="text-muted small mb-0">
        <?=$sub_title ?>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Profile Name</span><br>
                <span class="fw-semibold"><?= Html::encode($model->profile_name) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Payroll Scheme</span><br>
                <span class="fw-semibold"><?= Html::encode($model->payroll_mode) ?>
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Status</span><br>
                <span class="fw-semibold"><?= Html::encode($model->status->status_active) ?></span>
            </div>
            <div class="col-md-6">
                <span class="text-secondary small">Set Default</span><br>
                <?php
                if($model->is_default == 1){
                    print '<i class="fa fa-check text-success"></i>';
                }else{
                    print '<i class="fa fa-times text-muted"></i>';
                }
                ?>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Components</span><br>
                <div class="card">
                  <div class="card-body">
                    <div class="table-wrapper-80">
                        <table class="ct-table">
                          <tbody>
                            <?php
                            $i = 1;
                            foreach($item_id as $rows){
                            ?>
                            <tr>
                              <td class="card-category text-left"><i class="fa fa-check text-warning"></i> <?=$rows ?></td>
                            </tr>
                            <?php
                            $i++;
                            }
                            ?>
                          </tbody>
                        </table>
                    </div>
                  </div>
                </div>

            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Employees</span><br>
                <div class="card">
                  <div class="card-body">
                    <div class="table-wrapper-80">
                        <table class="ct-table">
                          <tbody>
                            <?php
                            $i = 1;
                            foreach($employee_id as $rows){
                            ?>
                            <tr>
                              <td class="card-category text-left"><i class="fa fa-check text-warning"></i> <?=$rows ?></td>
                            </tr>
                            <?php
                            $i++;
                            }
                            ?>
                          </tbody>
                        </table>
                    </div>
                  </div>
                </div>

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
<?php if (Yii::$app->request->isAjax): ?>

    <?= Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>

<?php else: ?>

    <?= Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
        'class' => 'btn btn-outline-secondary',
        'style' => 'min-width:140px;',
    ]) ?>

<?php endif; ?>
</div>
