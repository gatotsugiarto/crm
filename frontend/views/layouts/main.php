<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;
$currentController = Yii::$app->controller->id;
$currentAction = Yii::$app->controller->action->id;

// $menu_actions = ['index', 'about'];

$urlMenu = $currentController.'/'.$currentAction;
if($currentAction == 'index'){
    $baseMenu = 'Dashboard';
}else if($currentAction == 'about'){
    $baseMenu = 'About';
}else if($currentAction == 'contact'){
    $baseMenu = 'Contact';
}else if($currentAction == 'changepassword'){
    $baseMenu = 'Change Password';
} else {
    $baseMenu = 'Dashboard';
    $urlMenu = 'site/index';
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
        <div class="sidebar" data-image="<?=$baseUrl?>/img/sidebar-4.jpg" data-color="orange">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="<?=$baseUrl?>/site/index" class="simple-text">
                        Creative Tim
                    </a>
                </div>
                <ul class="nav">
                    <li class="nav-item <?= $currentController === 'site' && $currentAction === 'index' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= $baseUrl ?>/site/index">
                        <i class="nc-icon nc-chart-pie-35"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item <?= $currentController === 'site' && $currentAction === 'about' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= $baseUrl ?>/site/about">
                        <i class="nc-icon nc-circle-09"></i>
                        <p>About</p>
                    </a>
                </li>

                <li class="nav-item <?= $currentController === 'site' && $currentAction === 'contact' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= $baseUrl ?>/site/contact">
                        <i class="nc-icon nc-circle-09"></i>
                        <p>Contact</p>
                    </a>
                </li>
                    <li>
                        <a class="nav-link" href="./table.html">
                            <i class="nc-icon nc-notes"></i>
                            <p>Table List</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./typography.html">
                            <i class="nc-icon nc-paper-2"></i>
                            <p>Typography</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentController === 'site' && $currentAction === 'changepassword' ? 'active' : '' ?>">
                        <?= Html::a(
                            '<i class="nc-icon nc-key-25"></i><p>Change Password</p>',
                            'javascript:void(0);',
                            [
                                'class' => 'nav-link changepassword-member',
                                'data-url' => Url::to([$baseUrl.'/site/changepassword']),
                                'title' => 'Change Password',
                            ]
                        ); ?>
                    </li>
                    <li>
                        <a class="nav-link" href="./maps.html">
                            <i class="nc-icon nc-pin-3"></i>
                            <p>Maps</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./notifications.html">
                            <i class="nc-icon nc-bell-55"></i>
                            <p>Notifications</p>
                        </a>
                    </li>
                    <li class="nav-item active active-pro">
                        <a class="nav-link active" href="upgrade.html">
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
                    <a class="navbar-brand" href="<?=$baseUrl?>/<?=$urlMenu?>"> <?=$baseMenu ?> </a>
                    <button href="" class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-bar burger-lines"></span>
                        <span class="navbar-toggler-bar burger-lines"></span>
                        <span class="navbar-toggler-bar burger-lines"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">
                        <ul class="nav navbar-nav mr-auto">
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
                            <!--li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nc-icon nc-zoom-split"></i>
                                    <span class="d-lg-block">&nbsp;Search</span>
                                </a>
                            </li-->
                        </ul>
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
                            
                                
                                <?= Alert::widget() ?>
                                <?= $content ?>
                                
                            
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
                            <a href="http://www.creative-tim.com">Creative Tim</a>, made with love for a better web
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

    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered"> 
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-body p-4">
                    <!-- AJAX akan load form changepassword di sini -->
                </div>
            </div>
        </div>
    </div>

<?php $this->endBody() ?>

</body>
</html>

<?php
$script = <<<JS

function bindFormAjax() {
  $('#changepassword-form').off('submit').on('submit', function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: form.serialize(),
      dataType: 'json',
      success: function (response) {
        // bersihkan error lama
        form.find('.invalid-feedback').remove();
        form.find('.is-invalid').removeClass('is-invalid');

        if (response.success) {
          // tutup modal & reset form
          $('#changePasswordModal').modal('hide');
          form[0].reset();

          // tampilkan pesan sukses (opsional)
          $('.flash-message').html(
            '<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">' +
              response.message +
              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>'
          );

          // jika ada redirect dari server (misalnya ke login)
          if (response.redirect) {
            window.location.href = response.redirect;
          }
        } else {
          // tampilkan error validasi jika ada
          if (response.errors) {
            $.each(response.errors, function (key, messages) {
              var input = form.find('[name="ChangePasswordUser[' + key + ']"]');
              if (input.length) {
                input.addClass('is-invalid');
                var container = input.closest('.field-changepassworduser-' + key);
                if (container.find('.invalid-feedback').length === 0) {
                  container.append('<div class="invalid-feedback">' + messages[0] + '</div>');
                }
              }
            });
          }

          // tampilkan pesan umum gagal
          $('.flash-message').html(
            '<div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">' +
              response.message +
              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>'
          );
        }
      },
      error: function (xhr) {
        console.error('AJAX Error:', xhr);
        $('.flash-message').html(
          '<div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">' +
            'Terjadi kesalahan AJAX. Silakan coba lagi.' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
          '</div>'
        );
      }
    });
  });
}


$(document).on('click', '.changepassword-member', function() {
    var url = $(this).data('url');
    $('#changePasswordModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#changePasswordModal').modal('show');

    $.get(url, function(response) {
        $('#changePasswordModal .modal-body').html(response);
        bindFormAjax();
    });
});

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