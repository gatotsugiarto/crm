<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use backend\assets\BootboxAsset;
use common\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

AppAsset::register($this);
BootboxAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;
$currentController = Yii::$app->controller->id;
$currentAction = Yii::$app->controller->action->id;

$urlMenu = $currentController.'/'.$currentAction;
$menuMap = [
    'site/index'           => 'Dashboard',
    // 'menu/usermanagement' => 'User payrollmenu/index',
    'menu/usermanagement' => 'User Management',
    'logactivity/index'    => 'Log Activity',
    'menu/masterdata'    => 'Master Data',
    // 'payrollmenu/index'    => 'Payroll Management',
    'menu/productprice'    => 'Product & Pricing',
    'menu/sales'    => 'Sales CRM',
    'site/documentation'           => 'Documentation',
];

if (isset($menuMap[$urlMenu])) {
    $baseMenu = $menuMap[$urlMenu];
    $topUrl = "";
} else if (strpos($urlMenu, 'country/') === 0) {
    $baseMenu = 'Master Data';
    $topUrl = array_search('Master Data', $menuMap);
} else if (strpos($urlMenu, 'province/') === 0) {
    $baseMenu = 'Master Data';
    $topUrl = array_search('Master Data', $menuMap);
} else if (strpos($urlMenu, 'city/') === 0) {
    $baseMenu = 'Master Data';
    $topUrl = array_search('Master Data', $menuMap);
} else if (strpos($urlMenu, 'postalcode/') === 0) {
    $baseMenu = 'Master Data';
    $topUrl = array_search('Master Data', $menuMap);
} else if (strpos($urlMenu, 'applicationsetting/') === 0) {
    $baseMenu = 'Master Data';
    $topUrl = array_search('Master Data', $menuMap);
} else if (strpos($urlMenu, 'team/') === 0) {
    $baseMenu = 'Master Data';
    $topUrl = array_search('Master Data', $menuMap);

} else if (strpos($urlMenu, 'user/') === 0) {
    $baseMenu = 'User Management';
    $topUrl = array_search('User Management', $menuMap);
} else if (strpos($urlMenu, 'userassignment/') === 0) {
    $baseMenu = 'User Management';
    $topUrl = array_search('User Management', $menuMap);
} else if (strpos($urlMenu, 'rbac/') === 0) {
    $baseMenu = 'User Management';
    $topUrl = array_search('User Management', $menuMap);
} else if (strpos($urlMenu, 'member/') === 0) {
    $baseMenu = 'User Management';
    $topUrl = array_search('User Management', $menuMap);

} else if (strpos($urlMenu, 'lead/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'account/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'opportunity/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'opportunityproduct/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'opportunitystagehistory/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'activity/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'contact/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'accountaddress/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'quotation/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'quotationitem/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);
} else if (strpos($urlMenu, 'salesorder/') === 0) {
    $baseMenu = 'Sales CRM';
    $topUrl = array_search('Sales CRM', $menuMap);    

} else if (strpos($urlMenu, 'productcategory/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'product/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productprice/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'pricelist/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productdiscount/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'pricelist/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productbundleitem/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productuom/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);    

} else if (strpos($urlMenu, 'productcategory/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'product/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productprice/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'pricelist/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productdiscount/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'pricelist/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);
} else if (strpos($urlMenu, 'productbundleitem/') === 0) {
    $baseMenu = 'Product & Pricing';
    $topUrl = array_search('Product & Pricing', $menuMap);

} else {
    $baseMenu = 'Comm Corp';
    $topUrl = "";
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>

<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>    
    <div class="wrapper">
        <div class="sidebar" data-image="<?=$baseUrl?>/img/sidebar-4.jpg" data-color="blue">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="<?=$baseUrl?>/site/index" class="simple-text">
                        Comm-Corp v.1.0.7
                    </a>
                </div>
                <ul class="nav">
                    <li class="nav-item <?= $currentController === 'site' && $currentAction === 'index' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= $baseUrl ?>/site/index">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item <?= $currentController === 'menu' && $currentAction === 'usermanagement' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= $baseUrl ?>/menu/usermanagement">
                            <i class="nc-icon nc-circle-09"></i>
                            <p>User Management</p>
                        </a>
                    </li>

                    <!-- <li class="nav-item <?= $currentController === 'payrollmenu' && $currentAction === 'index' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= $baseUrl ?>/payrollmenu/index">
                            <i class="nc-icon nc-app"></i>
                            <p>Payroll Management</p>
                        </a>
                    </li> -->
                    <li class="nav-item <?= $currentController === 'menu' && $currentAction === 'masterdata' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= $baseUrl ?>/menu/masterdata">
                            <i class="nc-icon nc-layers-3"></i>
                            <p>Master Data</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentController === 'menu' && $currentAction === 'productprice' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= $baseUrl ?>/menu/productprice">
                            <i class="nc-icon nc-grid-45"></i>
                            <p>Product & Pricing</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentController === 'menu' && $currentAction === 'sales' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= $baseUrl ?>/menu/sales">
                            <i class="nc-icon nc-app"></i>
                            <p>Sales</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentController === 'satupayroll' && $currentAction === 'logactivity' ? 'active' : '' ?>">    
                        <a class="nav-link" href="<?= $baseUrl ?>/satupayroll/logactivity">
                            <i class="nc-icon nc-notes"></i>
                            <p>Log Activity</p>
                        </a>
                    </li>
                    <li class="nav-item active active-pro">
                        <a class="nav-link active" href="<?= $baseUrl ?>/site/documentation">    
                            <i class="nc-icon nc-alien-33"></i>
                            <p>Documentation</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg " color-on-scroll="500">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?=$baseUrl?>/<?=$topUrl?>"> <strong><?=$baseMenu ?></strong> </a>
                    <button href="" class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-bar burger-lines"></span>
                        <span class="navbar-toggler-bar burger-lines"></span>
                        <span class="navbar-toggler-bar burger-lines"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">
                        <ul class="nav navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="#" class="nav-link" title="Notification">
                                    <i class="nc-icon nc-bullet-list-67"></i>
                                </a>
                            </li>
                        </ul>
                        <!--ul class="nav navbar-nav mr-auto">
                            <li class="dropdown nav-item">
                                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                    <i class="nc-icon nc-planet"></i>
                                    <span class="notification">5</span>
                                    <span class="d-lg-none">Notification</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Notification 1</a>
                                    <a class="dropdown-item" href="#">Notification 2</a>
                                    <a class="dropdown-item" href="#">Notification 3</a>
                                    <a class="dropdown-item" href="#">Notification 4</a>
                                    <a class="dropdown-item" href="#">Another notification</a>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nc-icon nc-zoom-split"></i>
                                    <span class="d-lg-block">&nbsp;Search</span>
                                </a>
                            </li>
                        </ul-->
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                
                                <div class="navbar-nav ms-auto d-flex align-items-center">

                                    <?php
                                    if (Yii::$app->user->isGuest) {
                                    ?>
                                    
                                    <span class="no-icon">Login</span>

                                    <?php
                                    } else {
                                    ?>

                                    <div class="me-3">
                                        <!--a href="#" class="btn btn-icon btn-outline-primary rounded-circle shadow-sm" title="System Menu"><i class="fa fa-user-circle"></i></a-->
                                        
                                        <?= Html::a(
                                            '<i class="fa fa-user-circle"></i>',
                                            'javascript:void(0);',
                                            [
                                                'class' => 'btn btn-icon btn-outline-primary rounded-circle shadow-sm view-profile',
                                                'data-url' => Url::to([$baseUrl.'/site/profile']), // 🔹 absolute route
                                                'title' => 'My Profile',
                                            ]
                                        ); ?>
                                    </div>

                                    <div>
                                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'no-icon']). Html::submitButton('<i class="fa fa-sign-out-alt me-2"></i> Logout (' . Html::encode(Yii::$app->user->identity->username) . ')',['class' => 'btn btn-outline-warning fw-semibold px-3 py-2 logout-btn']). Html::endForm(); ?>
                                    </div>

                                    <?php
                                    }
                                    ?>

                                </div>

                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            
                                
                                <div class="page-wrapper">
    
                                    <div id="alert-container" class="mb-2">
                                        <?= \common\widgets\Alert::widget() ?>
                                    </div>
                                    <?= $content ?>
                                </div>
                                
                            
                        </div>
                    </div>
                    
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <nav>
                        <!--ul class="footer-menu">
                            <li>
                                <a href="#">
                                    Home
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Company
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Portfolio
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Blog
                                </a>
                            </li>
                        </ul-->
                        <p class="copyright text-center">
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script>
                            <a href="">PT. MNC Playemdia</a>, Efficient Sales, Pricing, and Customer Management
                        </p>
                    </nav>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Profile -->
    <div class="modal fade" id="viewProfile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-body p-4">
                    <!-- AJAX content here -->
                </div>
            </div>
        </div>
    </div>
<?php $this->endBody() ?>

</body>
</html>

<?php
$script = <<<JS
$(document).on('click', '.view-profile', function(e) {
    e.preventDefault();
    var url = $(this).data('url') || $(this).attr('href');
    if (!url) return;

    $('#viewProfile .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#viewProfile').modal('show');

    $.get(url, function(response) {
        $('#viewProfile .modal-body').html(response);
    }).fail(function() {
        $('#viewProfile .modal-body').html('<div class="alert alert-danger text-center">Failed to load content.</div>');
    });
});

// Pastikan tombol close bekerja
$(document).on('click', '[data-bs-dismiss="modal"]', function () {
    $('#viewProfile').modal('hide');
});

JS;
$this->registerJs($script);
?>

<script>
    // Ripple effect handler
    document.querySelectorAll('.menu-card').forEach(card => {
      card.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.classList.add('ripple');
        this.appendChild(ripple);

        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
        ripple.style.top = e.clientY - rect.top - size / 2 + 'px';

        setTimeout(() => ripple.remove(), 600);
      });
    });
  </script>
<?php $this->endPage();