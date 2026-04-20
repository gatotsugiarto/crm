<?php
use \yii\helpers\Url;

$this->title = 'Sales CRM';
$sub_title = 'Manage your sales pipeline and customer relationships';
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="container pt-0 pb-5">
  <!-- Header -->
  <div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="nc-icon nc-app"></i>&nbsp;&nbsp;<?= $this->title ?></h3>
    <p class="text-muted mb-0">
      <?= $sub_title ?>
    </p>
  </div>

  <div class="row g-4">
    
    <!-- Lead Management -->
    <?php if (\Yii::$app->user->can('backend.sales.lead.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/lead/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-app"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Lead Management</div>
          <div class="menu-desc text-muted">Manage and track potential customers</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>


    <!-- Account -->
    <?php if (\Yii::$app->user->can('backend.sales.account.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/account/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-single-02"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Accounts</div>
          <div class="menu-desc text-muted">Control and organize system accounts</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Contact -->
    <?php if (\Yii::$app->user->can('backend.sales.contact.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/contact/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-badge"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Contacts</div>
          <div class="menu-desc text-muted">Overview of people and organizations in your network</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>


    <!-- Account Address -->
    <?php if (\Yii::$app->user->can('backend.sales.accountaddress.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/accountaddress/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-map-big"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Account Address</div>
          <div class="menu-desc text-muted">List of addresses linked to customer accounts</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Opportunities -->
    <?php if (\Yii::$app->user->can('backend.sales.opportunity.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/opportunity/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-planet"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Opportunities</div>
          <div class="menu-desc text-muted">Track and manage all sales opportunities in one place</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>


    <!-- Opportunity Products  -->
    <?php if (\Yii::$app->user->can('backend.sales.opportunityproduct.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/opportunityproduct/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-ruler-pencil"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Opportunity Products</div>
          <div class="menu-desc text-muted">Manage products associated with each sales opportunity</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Opportunity Stage Histories -->
    <?php if (\Yii::$app->user->can('backend.sales.opportunitystagehistory.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/opportunitystagehistory/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-chart-bar-32"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Opportunity Stage Histories</div>
          <div class="menu-desc text-muted">Track the history of stage changes for each opportunity</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Product Category -->
    <?php if (\Yii::$app->user->can('backend.sales.activity.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/activity/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-time-alarm"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Activities</div>
          <div class="menu-desc text-muted">Track all activities related to your sales and operations</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Quotations -->
    <?php if (\Yii::$app->user->can('backend.sales.quotation.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/quotation/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-single-copy-04"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Quotations</div>
          <div class="menu-desc text-muted">List of quotations across all opportunities</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Quotation Items -->
    <?php if (\Yii::$app->user->can('backend.sales.quotationitem.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/quotationitem/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-bullet-list-67"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Quotation Items</div>
          <div class="menu-desc text-muted">Track all items associated with customer quotations</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Sales Order -->
    <?php if (\Yii::$app->user->can('backend.sales.salesorder.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/salesorder/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-cart-simple"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Sales Order</div>
          <div class="menu-desc text-muted">Manage and track confirmed customer orders</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Sales Order Item -->
    <?php if (\Yii::$app->user->can('backend.sales.salesorderitem.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/sales/salesorderitem/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-bullet-list-67"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Sales Order Items</div>
          <div class="menu-desc text-muted">List of products and services within orders</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

  </div>
</div>