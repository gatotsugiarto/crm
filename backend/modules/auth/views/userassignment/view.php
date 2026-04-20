<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\UserAssignment */

$this->title = 'Role Hierarchy';
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


function renderTree($tree) {
    echo "<li class='mb-1'>";
    echo "<i class='fa fa-sitemap text-primary'></i> " . Html::encode(str_replace('backend.', '', $tree['text']));
    if (!empty($tree['children'])) {
        echo "<ul class='ms-3'>";
        foreach ($tree['children'] as $child) {
            renderTree($child);
        }
        echo "</ul>";
    }
    echo "</li>";
}
?>

<div class="mb-3">
    <h5 class="text-primary fw-bold page-title">
        <i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;Role Hierarchy: <strong><span class="text-warning"><?= $description ?></span></strong>
    </h5>
    <p class="text-muted small mb-0">
        View and manage hierarchical roles and access permissions.
    </p>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <hr class="my-2">

        <!-- Tree Hierarchy -->
        <div class="mt-3">
            <h6 class="fw-bold text-secondary mb-2">
                <i class="fa fa-sitemap"></i> Role Tree
            </h6>
            <ul class="list-unstyled">
                <?php renderTree($tree); ?>
            </ul>
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

