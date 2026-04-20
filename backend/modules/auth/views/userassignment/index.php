<?php

use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;

$this->title = "User Assignments";
?>

<?php Pjax::begin(['id' => 'w0-pjax']); ?>

<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'user.fullname',
    'item_name',
];
?>

<!-- ALERT CONTAINER -->
<!-- <div id="alert-container"></div> -->

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="text-primary fw-bold page-title"><i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;<?= Html::encode($this->title) ?></div>
        <small class="text-muted">The following is the user with assignment in the system.</small>
    </div>
    <div>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'bsVersion' => '4',
            'bootstrap' => true,
            'filename' => 'User_Assignment_Export_' . date('YmdHis'),
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
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'panel' => false,
    'bordered' => false,
    'striped' => false,
    'condensed' => false,
    'hover' => true,
    'resizableColumns' => false,
    'export' => false,
    'responsiveWrap' => false,
    'tableOptions' => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn', 
            'header' => 'No',
        ],
        // [
        //     'attribute' => 'user_id',
        //     'format' => 'raw',
        //     'value' => fn($model) => Html::a(
        //         Html::encode($model->user->fullname),
        //         'javascript:void(0);',
        //         [
        //             'class' => 'text-primary view-data',
        //             'data-url' => Url::to(['view', 'item_name' => $model->item_name, 'user_id' => $model->user_id, 'description' => $model->itemName->description]),
        //         ]
        //     ),
        // ],
        [
            'attribute' => 'user_id',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a(
                    Html::encode($model->user->fullname),
                    [
                        'view',
                        'item_name' => $model->item_name,
                        'user_id' => $model->user_id,
                        'description' => $model->itemName->description,
                    ],
                    [
                        'class' => 'text-primary',
                        'data-pjax' => 0, // supaya tidak ditangkap PJAX
                    ]
                );
            },
        ],
        [
            'attribute' => 'item_name', 
            'format' => 'raw',
            'value' => function ($model) {
                if($model){
                    return $model->itemName->description;
                }else{
                    return '-';
                }
            },
        ],
        // [
        //     'label' => 'Sub Role',
        //     'value' => function($model) {
        //         $children = Yii::$app->authManager->getChildren($model->item_name);
        //         $descriptions = [];
        //         foreach ($children as $child) {
        //             $descriptions[] = $child->description ?: $child->name;
        //         }
        //         if($descriptions){
        //             return '['.implode('], [', $descriptions).']';
        //         }else{
        //             return '';
        //         }
        //     }
        // ],
        
    ],
]) ?>

<?php Pjax::end(); ?>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
/* =========================================================
 * VIEW JS
 * ======================================================= */
$(document).on('click', '.view-data', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});

JS);
?>

