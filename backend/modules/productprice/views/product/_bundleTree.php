<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\productprice\models\Product $product */
/** @var int[] $visited */

$items = $product->productBundleItems;
?>

<?php if ($items): ?>
<ul class="list-unstyled ps-4 mb-0 bundle-tree">
    <?php foreach ($items as $item): ?>
        <?php if (!$item->product) continue; ?>
        <?php $isCircular = in_array($item->product->id, $visited, true); ?>
        <li class="mb-1">
            <i class="fa fa-angle-right text-muted mr-1"></i>
            <small><?= Html::encode($item->product->name) ?></small>
            <small class="text-muted">&times; <?= (int) $item->quantity ?></small>

            <?php if ($isCircular): ?>
                <span class="badge bg-danger ms-1" title="This product is already an ancestor in this tree">circular reference</span>
            <?php elseif ($item->product->productBundleItems): ?>
                <?= $this->render('_bundleTree', [
                    'product' => $item->product,
                    'visited' => array_merge($visited, [$product->id]),
                ]) ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
    <small class="text-muted">&mdash; no components</small>
<?php endif; ?>
