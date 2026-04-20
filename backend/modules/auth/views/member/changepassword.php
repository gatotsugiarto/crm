<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\User */

$this->title = 'Change Password User: ' . ($model->_user->id ?? '');
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => ($model->_user->id ?? ''), 'url' => ['view', 'id' => ($model->_user->id ?? '')]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <?= $this->render('_form_changepassword', [
        'model' => $model,
    ]) ?>

</div>
