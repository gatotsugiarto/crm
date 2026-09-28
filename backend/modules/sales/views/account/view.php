<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\file\FileInput;
use common\modules\sales\models\AccountAttachment;

/** @var yii\web\View $this */
/** @var common\modules\sales\models\Account $model */
/** @var yii\data\ActiveDataProvider $attachmentDataProvider */

$user = Yii::$app->user;
$canWriteAccount = $user->can('backend.sales.account.*') || $user->can('root');
$canUploadAttachment = $canWriteAccount || $user->can('backend.sales.account.uploadattachment');
$canDeleteAttachment = $canWriteAccount || $user->can('backend.sales.account.deleteattachment');


$this->title = 'Detail '.'Accounts';
$sub_title = 'Control and organize system accounts';
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?= $this->title ?>
    </h5>
    <p class="text-muted small mb-0">
        <?=$sub_title ?>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Group Account</span><br>
                <span><small><?= Html::encode($model->parentAccount?->name ?? $model->name) ?></small></span>
            </div>

            <?php
            if($model->code){
                $code = '['.$model->code.'] ';
                $label = 'Code - Account Name';
            }else{
                $code = 'Account Name';
                $label = 'Account Name';
            }
            ?>
            <div class="col-md-6">
                <span class="text-secondary small"><?= $label?></span><br>
                <span><small><?= $code ?> <?= Html::encode($model->name) ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Customer Type</span><br>
                <span><small><?= Html::encode($model->account_type) ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Customer Segment</span><br>
                <span><small><?= Html::encode($model->customer_segment ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Industry</span><br>
                <span><small><?= Html::encode($model->industry ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Phone</span><br>
                <span><small><?= Html::encode($model->phone ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Email</span><br>
                <span><small><?= Html::encode($model->email ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Tax Number</span><br>
                <span><small><?= Html::encode($model->tax_number ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Website</span><br>
                <span><small><?= Html::encode($model->website ?? '-') ?></small></span>
            </div>
        </div>

        <hr class="my-2">

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="text-secondary small">Main Address</span><br>
                <span><small><?= Html::encode($model->address ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">City</span><br>
                <span><small><?= Html::encode($model->city?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Province</span><br>
                <span><small><?= Html::encode($model->province?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Country</span><br>
                <span><small><?= Html::encode($model->country?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Postal Code</span><br>
                <span><small><?= Html::encode($model->postalCode?->code ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Price List</span><br>
                <span><small><?= Html::encode($model->priceList?->name ?? '-') ?></small></span>
            </div>

            <div class="col-md-6">
                <span class="text-secondary small">Sales Team</span><br>
                <span><small><?= Html::encode($model->team?->name ?? '-') ?></small></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small">Assigned Sales</span><br>
                <span><small><?= Html::encode($model->assignedUser?->fullname ?? '-') ?></small></span>
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
                <i class="far fa-clock"></i> <small><?=Html::encode($model->updated_at) ?></small>
            </div>
        </div>

    </div>
</div>

<!-- =====================================================
     CONTACT SECTION
====================================================== -->
<?php Pjax::begin(['id' => 'pp-pjax']); ?>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-address-book"></i>&nbsp; Contacts
            </div>
            <small class="text-muted">Contact details for this Account</small>
        </div>
        <?= Html::button('<i class="fa fa-plus"></i> Add Contact', [
            'class'    => 'btn btn-primary btn-sm px-3 rounded-pill shadow-sm create-contact',
            'data-url' => Url::to(['/sales/contact/create', 'account_id' => $model->id]),
        ]) ?>
    </div>
    <?= $this->render('@backend/modules/sales/views/contact/_search', [
        'model' => $searchModel,
    ]) ?>
</div>

<div style="overflow-x:auto; width:100%;">
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'hover'            => true,
    'resizableColumns' => false,
    'export'           => false,
    'tableOptions'     => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout'           => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn', 'header' => 'No'],
        [
            'attribute' => 'fullname',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->fullname ?? ''),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['/sales/contact/view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute'      => 'job_title',
            'label'          => 'Job Title',
            'contentOptions' => ['class' => 'text-left'],
            'headerOptions'  => ['class' => 'text-white text-left'],
        ],
        [
            'attribute'      => 'email',
            'value'      => 'email',
            'contentOptions' => ['class' => 'text-left'],
            'headerOptions'  => ['class' => 'text-white text-left'],
        ],
        [
            'attribute'      => 'phone',
            'label'          => 'Phone',
            'contentOptions' => ['class' => 'text-left'],
            'headerOptions'  => ['class' => 'text-white text-left'],
        ],
        // [
        //     'attribute'      => 'mobile',
        //     'label'          => 'Mobile',
        //     'contentOptions' => ['class' => 'text-left'],
        //     'headerOptions'  => ['class' => 'text-white text-left'],
        // ],
        [
            'attribute' => 'is_primary',
            'format' => 'raw',
            'label' => 'Is Primary',
            'value' => function ($m) {
                if ($m->is_primary) {
                    return Html::a(
                        'Yes',
                        ['/sales/contact/unsetprimary', 'id' => $m->id, 'account_id' => $m->account_id],
                        [
                            'class' => 'badge badge-success',
                            'data-method' => 'post',
                            'data-confirm' => 'Unset primary contact?',
                        ]
                    );
                } else {
                    return Html::a(
                        'No',
                        ['/sales/contact/setprimary', 'id' => $m->id, 'account_id' => $m->account_id],
                        [
                            'class' => 'badge badge-secondary',
                            'data-method' => 'post',
                            'data-confirm' => 'Set primary contact?',
                        ]
                    );
                }
            },
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-white text-center'],
        ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Action',
            'template'       => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'buttons'        => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-contact',
                    'data-url' => Url::to(['/sales/contact/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-contact-js',
                    'data-url'  => Url::to(['/sales/contact/delete', 'id' => $m->id]),
                    'data-name' => ($m->product->fullname ?? '-'),
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- =====================================================
     ACCOUNT ADDRESS
====================================================== -->
<?php Pjax::begin(['id' => 'address-pjax']); ?>

<div class="mb-3 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-bold" style="font-size: 15px;">
                <i class="fa fa-address-card"></i>&nbsp; Other Address
            </div>
            <small class="text-muted">Overview of account address information</small>
        </div>
        <?= Html::button('<i class="fa fa-plus"></i> Add Address', [
            'class'    => 'btn btn-warning btn-sm px-3 rounded-pill shadow-sm create-address',
            'data-url' => Url::to(['/sales/accountaddress/create', 'account_id' => $model->id]),
        ]) ?>
    </div>
    <?= $this->render('@backend/modules/sales/views/accountaddress/_search', [
        'model' => $addressSearchModel,
    ]) ?>
</div>

<div style="overflow-x:auto; width:100%;">
<?= GridView::widget([
    'dataProvider' => $addressDataProvider,
    'hover'            => true,
    'resizableColumns' => false,
    'export'           => false,
    'tableOptions'     => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout'           => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn', 'header' => 'No'],
        [
            'attribute' => 'account_id',
            'label'     => 'Account',
            'value'     => fn($m) => $m->account->name ?? '<span class="text-muted fst-italic">All Accounts</span>',
            'format'    => 'raw',
        ],
        // [
        //     'attribute'      => 'discount_type',
        //     'label'          => 'Type',
        //     'format'         => 'raw',
        //     'value'          => fn($m) => $m->discount_type === 'percent'
        //         ? Html::tag('span', 'Percent', ['class' => 'badge badge-info'])
        //         : Html::tag('span', 'Amount',  ['class' => 'badge badge-secondary']),
        //     'contentOptions' => ['class' => 'text-center'],
        //     'headerOptions'  => ['class' => 'text-white text-center'],
        // ],
        // [
        //     'attribute'      => 'discount_value',
        //     'label'          => 'Value',
        //     'format'         => 'raw',
        //     'value'          => fn($m) => $m->discount_type === 'percent'
        //         ? Yii::$app->formatter->asDecimal($m->discount_value, 2) . '%'
        //         : Yii::$app->formatter->asDecimal($m->discount_value, 2),
        //     'contentOptions' => ['class' => 'text-right'],
        //     'headerOptions'  => ['class' => 'text-white text-right'],
        // ],
        [
            'attribute'      => 'address_type',
            'label'          => 'Address Type',
            'value'     => fn($m) => $m->displayAddressType(),
            'contentOptions' => ['class' => 'text-left'],
            'headerOptions'  => ['class' => 'text-white text-left'],
        ],
        [
            'attribute' => 'address',
            'format' => 'raw',
            'value' => fn($model) => Html::a(
                Html::encode($model->address ?? ''),
                'javascript:void(0);',
                [
                    'class' => 'text-primary view-data',
                    'data-url' => Url::to(['/sales/accountaddress/view', 'id' => $model->id]),
                ]
            ),
        ],
        [
            'attribute' => 'city_id',
            'value' => function ($model) {
                return $model->city?->name ?? '-';
            },
        ],



        // [
        //     'attribute'      => 'valid_from',
        //     'label'          => 'Valid From',
        //     'contentOptions' => ['class' => 'text-center'],
        //     'headerOptions'  => ['class' => 'text-white text-center'],
        // ],
        // [
        //     'attribute'      => 'valid_to',
        //     'label'          => 'Valid To',
        //     'contentOptions' => ['class' => 'text-center'],
        //     'headerOptions'  => ['class' => 'text-white text-center'],
        // ],
        // [
        //     'attribute'      => 'is_stackable',
        //     'label'          => 'Stackable',
        //     'format'         => 'raw',
        //     'value'          => fn($m) => $m->is_stackable
        //         ? Html::tag('span', 'Yes', ['class' => 'badge badge-success'])
        //         : Html::tag('span', 'No',  ['class' => 'badge badge-secondary']),
        //     'contentOptions' => ['class' => 'text-center'],
        //     'headerOptions'  => ['class' => 'text-white text-center'],
        // ],
        // [
        //     'attribute'      => 'status_id',
        //     'format'         => 'raw',
        //     'label'          => 'Status',
        //     'value'          => fn($m) => $m->status_id == 1
        //         ? Html::tag('span', 'Active',     ['class' => 'badge badge-success'])
        //         : Html::tag('span', 'Non Active', ['class' => 'badge badge-secondary']),
        //     'contentOptions' => ['class' => 'text-center'],
        //     'headerOptions'  => ['class' => 'text-white text-center'],
        // ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Action',
            'template'       => '{update} {delete}',
            'contentOptions' => ['class' => 'text-center'],
            'buttons'        => [
                'update' => fn($url, $m) => Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0);', [
                    'class'    => 'btn btn-sm btn-outline-success rounded-circle edit-address',
                    'data-url' => Url::to(['/sales/accountaddress/update', 'id' => $m->id]),
                    'title'    => 'Edit',
                ]),
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-address-js',
                    'data-url'  => Url::to(['/sales/accountaddress/delete', 'id' => $m->id]),
                    'data-name' => $m->account->name . ' - ' . $m->address_type,
                ]),
            ],
        ],
    ],
]) ?>
</div>

<?php Pjax::end(); ?>

<!-- =====================================================
     DOCUMENTS (account_attachment)
====================================================== -->
<div class="mb-3 mt-4">
    <div class="mb-2">
        <div class="fw-bold" style="font-size: 15px;">
            <i class="fa fa-paperclip"></i>&nbsp; Documents
        </div>
        <small class="text-muted">Files related to this client/partner (max <?= Yii::$app->formatter->asShortSize(Yii::$app->params['accountAttachmentMaxSize']) ?> per file)</small>
    </div>

    <?php if ($canUploadAttachment): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body">
                <div class="form-group mb-2">
                    <?= Html::textInput('attachment_description', '', [
                        'id' => 'attachment-description',
                        'class' => 'form-control form-control-sm',
                        'maxlength' => 255,
                        'placeholder' => 'Description (optional), e.g. Contract 2026, NPWP, Company Profile',
                    ]) ?>
                </div>
                <?= FileInput::widget([
                    'name' => 'file',
                    'id' => 'attachment-file',
                    'options' => ['multiple' => true],
                    'pluginOptions' => [
                        'uploadUrl' => Url::to(['uploadattachment', 'id' => $model->id]),
                        'uploadAsync' => true,
                        'uploadExtraData' => new \yii\web\JsExpression('function () {
                            return {
                                description: $("#attachment-description").val(),
                                ' . json_encode(Yii::$app->request->csrfParam) . ': yii.getCsrfToken()
                            };
                        }'),
                        'allowedFileExtensions' => AccountAttachment::EXTENSIONS,
                        'maxFileSize' => (int) (Yii::$app->params['accountAttachmentMaxSize'] / 1024),
                        'maxFileCount' => 10,
                        'showPreview' => true,
                        'showRemove' => true,
                        'showCancel' => true,
                        'browseLabel' => 'Choose files',
                        'uploadLabel' => 'Upload',
                        'browseClass' => 'btn btn-outline-primary btn-sm',
                        'uploadClass' => 'btn btn-primary btn-sm',
                        'removeClass' => 'btn btn-outline-secondary btn-sm',
                        'previewFileType' => 'any',
                        'fileActionSettings' => ['showZoom' => false, 'showDrag' => false],
                        // uploaded files leave the preview; failed ones stay with their error
                        'showUploadedThumbs' => false,
                    ],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php Pjax::begin(['id' => 'attachment-pjax']); ?>
<div style="overflow-x:auto; width:100%;">
<?= GridView::widget([
    'dataProvider'     => $attachmentDataProvider,
    'hover'            => true,
    'resizableColumns' => false,
    'export'           => false,
    'emptyText'        => 'No documents yet.',
    'tableOptions'     => ['class' => 'table table-hover table-striped align-middle shadow-sm'],
    'layout'           => "{items}\n<div class='d-flex justify-content-between align-items-center mt-2'>{pager}{summary}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn', 'header' => 'No'],
        [
            'attribute' => 'file_name',
            'format'    => 'raw',
            'value'     => fn($m) => Html::a(
                '<i class="fa fa-download"></i> ' . Html::encode($m->file_name),
                ['downloadattachment', 'id' => $m->id],
                ['class' => 'text-primary', 'data-pjax' => '0']
            ),
        ],
        [
            'attribute' => 'description',
            'value'     => fn($m) => $m->description ?: '-',
        ],
        [
            'attribute'      => 'file_size',
            'value'          => fn($m) => $m->file_size !== null ? Yii::$app->formatter->asShortSize($m->file_size) : '-',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions'  => ['class' => 'text-white text-right'],
        ],
        [
            'attribute' => 'created_by',
            'value'     => fn($m) => $m->createdBy?->fullname ?? '-',
        ],
        [
            'attribute' => 'created_at',
            'value'     => fn($m) => $m->created_at ? Yii::$app->formatter->asDatetime($m->created_at) : '-',
        ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Action',
            'template'       => '{delete}',
            'visible'        => $canDeleteAttachment,
            'contentOptions' => ['class' => 'text-center'],
            'buttons'        => [
                'delete' => fn($url, $m) => Html::button('<i class="fa fa-trash"></i>', [
                    'class'     => 'btn btn-sm btn-outline-danger rounded-circle delete-attachment-js',
                    'data-url'  => Url::to(['deleteattachment', 'id' => $m->id]),
                    'data-name' => $m->file_name,
                ]),
            ],
        ],
    ],
]) ?>
</div>
<?php Pjax::end(); ?>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>


<!-- =======================================================
     MODALS: PRODUCT PRICE
======================================================== -->
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePriceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-price-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-price-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Delete', [
                        'class' => 'btn btn-danger px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODALS: ACCOUNT ADDRESS
======================================================== -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-4"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteaddressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-address-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-address-form" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Delete', [
                        'class' => 'btn btn-danger px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure want to delete <strong id="delete-attachment-name"></strong>?
            </div>
            <div class="modal-footer">
                <?= Html::button('<i class="fa fa-times"></i> Cancel', [
                    'class' => 'btn btn-outline-secondary mr-2 px-4',
                    'data-dismiss' => 'modal',
                    'style' => 'min-width:140px;',
                ]) ?>
                <form id="delete-attachment-form" method="post">
                    <?= Html::submitButton('<i class="fa fa-trash"></i> Delete', [
                        'class' => 'btn btn-danger px-4',
                        'style' => 'min-width:140px;',
                    ]) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS

$(document).on('click', '.view-data', function() {
    $('#viewModal').modal('show').find('.modal-body').load($(this).data('url'));
});    

/* =========================================================
 * CONTACT — ADD/EDIT/DELETE
 * ======================================================= */
$(document).on('click', '.create-contact, .edit-contact', function () {
    $('#priceModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#priceModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#contact-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#priceModal').modal('hide');
            $.pjax.reload({container: '#pp-pjax', timeout: 500}).done(function () {
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            });
        } else if (res && res.errors) {
            form.yiiActiveForm('updateMessages', res.errors, true);
        }
    }, 'json');
    return false;
});

$(document).on('click', '.delete-contact-js', function () {
    $('#delete-price-name').text($(this).data('name'));
    $('#delete-price-form').attr('action', $(this).data('url'));
    $('#deletePriceModal').modal('show');
});

$(document).on('submit', '#delete-price-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deletePriceModal').modal('hide');
        $.pjax.reload({container: '#pp-pjax', timeout: 500}).done(function () {
            if (res && res.success) {
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            }
        });
    }, 'json');
});

/* =========================================================
 * ACCOUNT ADDRESS — ADD/EDIT/DELETE
 * ======================================================= */
$(document).on('click', '.create-address, .edit-address', function () {
    $('#addressModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#addressModal').modal('show').find('.modal-body').load($(this).data('url'));
});

$(document).on('beforeSubmit', '#accountaddress-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
        if (res && res.success) {
            $('#addressModal').modal('hide');
            $.pjax.reload({container: '#address-pjax', timeout: 500}).done(function () {
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            });
        } else if (res && res.errors) {
            form.yiiActiveForm('updateMessages', res.errors, true);
        }
    }, 'json');
    return false;
});

$(document).on('click', '.delete-address-js', function () {
    $('#delete-address-name').text($(this).data('name'));
    $('#delete-address-form').attr('action', $(this).data('url'));
    $('#deleteaddressModal').modal('show');
});

$(document).on('submit', '#delete-address-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deleteaddressModal').modal('hide');
        $.pjax.reload({container: '#address-pjax', timeout: 500}).done(function () {
            if (res && res.success) {
                $('#alert-container').html(
                    '<div class="alert alert-success alert-dismissible fade show mt-3">' +
                    '<i class="fa fa-check-circle"></i> ' + (res.message || 'Operation successful.') +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            }
        });
    }, 'json');
});

/* =========================================================
 * DOCUMENTS — UPLOAD (Kartik FileInput) / DELETE
 * ======================================================= */
function showAttachmentAlert(type, icon, message) {
    $('#alert-container').html(
        '<div class="alert alert-' + type + ' alert-dismissible fade show mt-3">' +
        '<i class="fa ' + icon + '"></i> ' + message +
        '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
    );
}

// uploadAsync sends one request per file; refresh the list once the batch is done
var attachmentsUploaded = 0;
$('#attachment-file').on('fileuploaded', function () {
    attachmentsUploaded++;
});
$('#attachment-file').on('filebatchuploadcomplete', function () {
    if (!attachmentsUploaded) return;
    var n = attachmentsUploaded;
    attachmentsUploaded = 0;
    $('#attachment-description').val('');
    $.pjax.reload({container: '#attachment-pjax', timeout: 500}).done(function () {
        showAttachmentAlert('success', 'fa-check-circle', n + (n > 1 ? ' documents' : ' document') + ' uploaded.');
    });
});

$(document).on('click', '.delete-attachment-js', function () {
    $('#delete-attachment-name').text($(this).data('name'));
    $('#delete-attachment-form').attr('action', $(this).data('url'));
    $('#deleteAttachmentModal').modal('show');
});

$(document).on('submit', '#delete-attachment-form', function (e) {
    e.preventDefault();
    $.post($(this).attr('action'), {_csrf: yii.getCsrfToken()}, function (res) {
        $('#deleteAttachmentModal').modal('hide');
        $.pjax.reload({container: '#attachment-pjax', timeout: 500}).done(function () {
            if (res && res.success) {
                showAttachmentAlert('success', 'fa-check-circle', res.message);
            }
        });
    }, 'json');
});

JS);
?>  
