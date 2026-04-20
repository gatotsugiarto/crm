<?php
use \yii\helpers\Url;

$this->title = 'Product & Pricing';
$sub_title = 'Overview of products and pricing configurations';
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="container pt-0 pb-5">
  <!-- Header -->
  <div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="nc-icon nc-grid-45"></i>&nbsp;&nbsp;<?= $this->title ?></h3>
    <p class="text-muted mb-0">
      <?= $sub_title ?>
    </p>
  </div>

  <div class="row g-4">

    
    <!-- Product Bundles -->
    <?php if (\Yii::$app->user->can('backend.productprice.pricelist.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/productbundleitem/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-app"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Product Bundles</div>
          <div class="menu-desc text-muted">Manage bundled products and packages</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>


    <!-- Price Lists -->
    <?php if (\Yii::$app->user->can('backend.productprice.pricelist.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/pricelist/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-notes"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Price Lists</div>
          <div class="menu-desc text-muted">Centralized list of product pricing</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Product Discount -->
    <?php if (\Yii::$app->user->can('backend.productprice.productdiscount.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/productdiscount/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-chart-bar-32"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Product Discounts</div>
          <div class="menu-desc text-muted">Set and control product discounts</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>


    <!-- Product Price  -->
    <?php if (\Yii::$app->user->can('backend.productprice.productprice.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/productprice/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-tag-content"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Product Prices</div>
          <div class="menu-desc text-muted">Product pricing management system</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Product -->
    <?php if (\Yii::$app->user->can('backend.productprice.product.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/product/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-cart-simple"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Products</div>
          <div class="menu-desc text-muted">Organize and control product data</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Product Category -->
    <?php if (\Yii::$app->user->can('backend.productprice.productcategory.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/productcategory/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-layers-3"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Product Categories</div>
          <div class="menu-desc text-muted">Define and manage product categories</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Product UOM -->
    <?php if (\Yii::$app->user->can('backend.productprice.productuom.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/productprice/productuom/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-scissors"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Product UOMs</div>
          <div class="menu-desc text-muted">Define and organize product measurement units</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

  </div>
</div>