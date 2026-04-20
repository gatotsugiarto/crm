<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\Member */

$this->title = 'Create New Member';
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-create">

    <?= $this->render('_form', [
        'model' => $model,
        'formToken' => $formToken,
    ]) ?>

</div>
