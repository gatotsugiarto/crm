<?php
use \yii\helpers\Url;

$this->title = 'Master Data';
$sub_title = 'Manage your core data including locations, configurations, and team structures';
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="container pt-0 pb-5">
  <!-- Header -->
  <div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="nc-icon nc-layers-3"></i>&nbsp;&nbsp;<?= $this->title ?></h3>
    <p class="text-muted mb-0">
      <?= $sub_title ?>
    </p>
  </div>

  <div class="row g-4">


    <!-- Postal Code -->
    <?php if (\Yii::$app->user->can('backend.master.postalcode.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/postalcode/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-delivery-fast"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Postal Code</div>
          <div class="menu-desc text-muted">Handle postal code records efficiently</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- City -->
    <?php if (\Yii::$app->user->can('backend.master.city.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/city/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-compass-05"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Cities</div>
          <div class="menu-desc text-muted">Handle city records and details</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>


    <!-- Province  -->
    <?php if (\Yii::$app->user->can('backend.master.province.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/province/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-pin-3"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Provinces</div>
          <div class="menu-desc text-muted">Organize regional province information</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Country -->
    <?php if (\Yii::$app->user->can('backend.master.country.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/country/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-bank"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Countries</div>
          <div class="menu-desc text-muted">Manage country data and information</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Application Setting -->
    <?php if (\Yii::$app->user->can('backend.master.applicationsetting.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/applicationsetting/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-settings-gear-64"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Application Setting</div>
          <div class="menu-desc text-muted">Configure application parameters and controls</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <?php if (\Yii::$app->user->can('backend.master.team.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/team/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-single-02"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Sales Teams</div>
          <div class="menu-desc text-muted">Team management system</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

  </div>
</div>