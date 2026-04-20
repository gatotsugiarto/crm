<?php

use common\models\Member;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\MemberSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Members';
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Member', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            'email:email',
            //'verification_token',
            'status',
            //'created_at',
            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {suspend} {resend}',
                'buttons' => [
                    'suspend' => function ($url, $model) {
                        return Html::a('<i class="fa fa-ban"></i>', ['suspend', 'id' => $model->id], [
                            'title' => 'Suspend member',
                            'data-confirm' => 'Yakin suspend member ini?',
                            'data-toggle' => 'tooltip',
                        ]);
                    },
                    'resend' => function ($url, $model) {
                        return Html::a('<i class="fa fa-paper-plane"></i>', ['resend', 'id' => $model->id], [
                            'title' => 'Kirim verifikasi',
                            'data-confirm' => 'Kirim ulang email verifikasi?',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>


</div>
