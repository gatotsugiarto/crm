<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\select2\Select2;

$this->title = "Employee Management";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
    $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'region_id',
            //'region',
            //'company_id',
            //'company',
            //'branch_id',
            //'branch',
            //'site_office_id',
            //'site_office',
            //'department_id',
            //'department',
            //'division_id',
            //'division',
            'e_number',
            [
                'attribute' => 'fullname',
                'format' => 'raw',
                'value' => fn($model) => Html::a(
                    Html::encode($model->fullname),
                    'javascript:void(0);',
                    [
                        'class' => 'text-primary view-employee',
                        'data-url' => Url::to(['view', 'id' => $model->id]),
                    ]
                ),
            ],
            [
                'attribute'=>'join_date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDateTime($data->join_date, 'php:d/m/Y');
                },
            ],
            //'grade_id',
            'jabatan',
            'grade',
            //'level_jabatan_id',
            //'level_jabatan',
            //'jabatan_id',
            
            //'marital_status_id',
            // 'marital_status',
            //'family_status_id',
            // 'family_status',
            'email:email',
            //'is_npwp',
            //'npwp_id',
            //'bpjs_tk:ntext',
            //'bpjs_kes:ntext',
            //'bank_id',
            //'bank',
            //'bank_no',
            //'employee_status_id',
            //'employee_status',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status_id == 1) {
                        return Html::a(
                            Html::tag('span', 'Active', ['class' => 'badge badge-success']),
                            'javascript:void(0);',
                            [
                                'class' => 'text-primary nonactive-user',
                                'data-url' => Url::to(['nonactive', 'id' => $model->id]),
                                'title' => 'Non Active',
                                'data-name' => $model->fullname,
                            ]
                        );
                    } else {
                        return Html::a(
                            Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
                            'javascript:void(0);',
                            [
                                'class' => 'text-primary reactive-user',
                                'data-url' => Url::to(['reactive', 'id' => $model->id]),
                                'title' => 'Reactivate',
                                'data-name' => $model->fullname,
                            ]
                        );
                    }
                },
                'filterType' => GridView::FILTER_SELECT2,
                // 'filter' => [10 => 'Active', 9 => 'Non Active', 0 => 'Delete'],
                'filter' => yii\helpers\ArrayHelper::map(\common\modules\master\models\Status::find()->orderBy('id')->asArray()->all(),'id','status'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => 'All Status',
                    ],
                    'options' => ['placeholder' => 'All Status'],
                ],
                'filterInputOptions' => ['class' => 'form-control'],
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-white bg-creative text-center'],
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ];

        $gridColumnsDownload = [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'region_id',
            'e_number',
            'fullname',
            'region',
            //'company_id',
            'company',
            //'branch_id',
            'branch',
            //'site_office_id',
            'site_office',
            //'department_id',
            'department',
            //'division_id',
            'division',
            [
                'attribute'=>'join_date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDateTime($data->join_date, 'php:d/m/Y');
                },
            ],
            //'grade_id',
            'grade',
            //'level_jabatan_id',
            'level_jabatan',
            //'jabatan_id',
            'jabatan',
            //'marital_status_id',
            'marital_status',
            //'family_status_id',
            'family_status',
            'email:email',
            [
                'attribute'=>'is_npwp',
                'value' => function ($data) {
                    if($data->is_npwp){
                        return 'Ya';
                    }else{
                        return 'Tidak';
                    }
                },
            ],
            'npwp_id',
            //'bpjs_tk:ntext',
            //'bpjs_kes:ntext',
            //'bank_id',
            'bank',
            'bank_no',
            //'employee_status_id',
            'employee_status',
            //'status_id',
            'created_at',
            //'created_by',
            'updated_at',
            //'updated_by',

            //['class' => 'yii\grid\ActionColumn'],
        ];
    ?>

<!-- ALERT CONTAINER -->
<div id="alert-container"></div>

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">Manage and monitor employee data within the system</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumnsDownload,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'Master_Employee_'.date('YmdHis'),
            'showColumnSelector' => false,
            'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_EXCEL => false,
            ],
            'dropdownOptions' => [
                'label' => '<i class="fa fa-download"></i> Export',
                'class' => 'btn btn-sm btn-success rounded-pill shadow-sm',
                'style' => 'min-width:160px;',
                'encodeLabel' => false,
            ],
        ]) ?>

        <?php
        //  Html::button('<i class="fa fa-plus"></i> New Company', [
        //     'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm create-company',
        //     'style' => 'min-width:160px;',
        //     'data-url' => Url::to(['create']),
        // ]) 
        ?>
    </div>
</div>

<!-- GRIDVIEW -->
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'hover' => true,
    'resizableColumns' => false,
    'export' => false,
    'tableOptions' => [
        'class' => 'table table-hover table-striped align-middle shadow-sm'
    ],
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => $gridColumns,
]) ?>

<?php Pjax::end(); ?>


<!-- APP MODAL -->
<div class="modal fade" id="appModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmNonActiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Non Active Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to non active employee <strong id="nonactive-user-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="nonactive-user-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Non Active', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmReactiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Re Active Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to re active employee <strong id="reactive-user-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>

                <form id="reactive-user-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-refresh"></i> Re Active', [
                        'class' => 'btn btn-warning px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// ====================== JAVASCRIPT ======================
$this->registerJs(<<<JS

$(document).on('click', '.view-employee', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('click', '.nonactive-user', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#nonactive-user-name').text(name);
    $('#nonactive-user-form').attr('action', url);
    $('#confirmNonActiveModal').modal('show');
});

$(document).on('click', '.reactive-user', function() {
    var url = $(this).data('url');
    var name = $(this).data('name');

    $('#reactive-user-name').text(name);
    $('#reactive-user-form').attr('action', url);
    $('#confirmReactiveModal').modal('show');
});

JS);
?>
