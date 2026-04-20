<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var <?= ltrim($generator->modelClass, '\\') ?> $model */

<?php 
$label = $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))));
?>

$this->title = 'Detail '.<?= $label?>;
$sub_title = $this->title;
$this->params['breadcrumbs'][] = ['label' => <?= $label ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?= "<?=" ?> $this->title ?>
    </h5>
    <p class="text-muted small mb-0">
        <?= "<?=" ?>$sub_title ?>
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
<?php
$fields = [];
$exclude_fields = ['id','status_id','created_at','created_by','updated_at','updated_by'];

if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (!in_array($name, $exclude_fields)) $fields[] = $name;
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        if (!in_array($column->name, $exclude_fields)) $fields[] = $column->name;
    }
}

$total = count($fields);
?>

<?php for ($i = 0; $i < $total; $i += 2): ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-secondary small"><?= Inflector::humanize($fields[$i]) ?></span><br>
                <span><small><?= "<?=" ?> Html::encode($model-><?= $fields[$i] ?>) ?></small></span>
            </div>

        <?php if (isset($fields[$i+1])): ?>
    <div class="col-md-6">
                <span class="text-secondary small"><?= Inflector::humanize($fields[$i+1]) ?></span><br>
                <span><small><?= "<?=" ?> Html::encode($model-><?= $fields[$i+1] ?>) ?></small></span>
            </div>
        <?php endif; ?>
</div>

<?php endfor; ?>

        <hr class="my-2">

        <div class="row mb-2 small">
            <div class="col-md-6 text-muted">
                <i class="fa fa-plus-circle"></i> Created by:
                <strong><?= "<?=" ?> Html::encode($model->createdBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= "<?=" ?> Html::encode($model->created_at) ?></small>
            </div>
            <div class="col-md-6 text-muted">
                <i class="fa fa-edit"></i> Updated by:
                <strong><?= "<?=" ?> Html::encode($model->updatedBy->fullname) ?></strong>
                <br>
                <i class="far fa-clock"></i> <small><?= "<?=" ?>Html::encode($model->updated_at) ?></small>
            </div>
        </div>

    </div>
</div>

<div class="text-end mt-3">
<?= "<?php" ?> if (Yii::$app->request->isAjax): ?>

    <?= "<?=" ?> Html::button('<i class="fa fa-times"></i> Close', [
        'class' => 'btn btn-outline-secondary',
        'data-dismiss' => 'modal',
        'style' => 'min-width:140px;',
    ]) ?>

<?= "<?php" ?> else: ?>

    <?= "<?=" ?> Html::a('<i class="fa fa-arrow-left"></i> Back', 'javascript:history.back()', [
        'class' => 'btn btn-outline-secondary',
        'style' => 'min-width:140px;',
    ]) ?>

<?= "<?php" ?> endif; ?>
</div>
