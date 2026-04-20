<?php
use \yii\helpers\Url;

$this->title = 'Payroll Management';
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="container py-5">
  <div class="row g-4">
    
    <!-- Upload -->
    <?php if (\Yii::$app->user->can('backend.payroll.payroll.reportupload') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/payroll/reportupload" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-cloud-upload-94"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Upload Data</div>
          <div class="menu-desc text-muted">Upload payroll data integrated from HRIS</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Join Resign -->
    <?php if (\Yii::$app->user->can('backend.payroll.payroll.joinresign') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/payroll/joinresign" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-badge"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Join & Resignation</div>
          <div class="menu-desc text-muted">Overview of joined and resigned employees for the period</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Salary -->
    <?php if (\Yii::$app->user->can('backend.master.salary.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/salary/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-money-coins"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Employee Salary</div>
          <div class="menu-desc text-muted">Configure and oversee salary structures and compensation records</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Employee -->
    <?php if (\Yii::$app->user->can('backend.master.employee.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/employee/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-circle-09"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Employee Management</div>
          <div class="menu-desc text-muted">Manage and monitor employee data within the system</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Employee Payroll Profile -->
    <?php if (\Yii::$app->user->can('backend.master.employeepayrollprofile.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/master/employeepayrollprofile/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-app"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Employee Profile</div>
          <div class="menu-desc text-muted">Employee information and payroll details</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Payroll -->
    <?php if (\Yii::$app->user->can('backend.payroll.payroll.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/payroll/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-credit-card"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Payroll</div>
          <div class="menu-desc text-muted">Manages employee salary and payroll processing</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- L3 Summary -->
    <?php if (\Yii::$app->user->can('backend.payroll.payrolldetaill3.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/payrolldetaill3/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-chart-bar-32"></i>
          </div>
          <div class="menu-title fw-bold text-primary">L3 - Summary</div>
          <div class="menu-desc text-muted">Consolidated Payroll Overview</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Bukti Potong PPh21 -->
    <?php if (\Yii::$app->user->can('backend.payroll.buktipotongpph21.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/buktipotongpph21/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-single-copy-04"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Bukti Potong PPh21</div>
          <div class="menu-desc text-muted">Form 1721-A1 / A2. Bukti Resmi Pemotongan PPh Pasal 21</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Formulir 1721-A1 -->
    <?php if (\Yii::$app->user->can('backend.payroll.formulir1721a1.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/formulir1721a1/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-single-copy-04"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Formulir 1721-A1</div>
          <div class="menu-desc text-muted">Bukti Pemotongan PPh Pasal 21 Pegawai Tetap</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- BPJS Filing -->
    <?php if (\Yii::$app->user->can('backend.payroll.bpjsfiling.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/bpjsfiling/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-bank"></i>
          </div>
          <div class="menu-title fw-bold text-primary">BPJS Records</div>
          <div class="menu-desc text-muted">Employee BPJS records and contribution details.</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- BPJS Filing -->
    <?php if (\Yii::$app->user->can('backend.payroll.payrollthr.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/payrollthr/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-money-coins"></i>
          </div>
          <div class="menu-title fw-bold text-primary">THR</div>
          <div class="menu-desc text-muted">Employee THR summary and contribution information.</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

    <!-- Additional Benefit -->
    <?php if (\Yii::$app->user->can('backend.payroll.additionalbenefit.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/payroll/additionalbenefit/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-atom"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Additional Benefit</div>
          <div class="menu-desc text-muted">Overview of extra employee benefits.</div>
        </div>
      </a>
    </div>
    <?php
    }
    ?>

  </div>
</div>