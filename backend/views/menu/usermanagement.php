<?php
use \yii\helpers\Url;

$this->title = 'User Management';
$sub_title = 'Manage system users and their access';
$baseUrl = Yii::$app->request->baseUrl;
?>

<div id="alert-container" class="mb-2">
  <div class="flash-message"></div>
</div>
<div class="container pt-0 pb-5">

  <!-- Header -->
  <div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="nc-icon nc-app"></i>&nbsp;&nbsp;<?= $this->title ?></h3>
    <p class="text-muted mb-0">
      <?= $sub_title ?>
    </p>
  </div>

  <div class="row g-4">

    <!-- change password -->
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="javascript:void(0);" 
         class="text-decoration-none changepassword-user"
         data-url="<?= Url::to(['/menu/changepassword']) ?>">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-key-25"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Change Password</div>
          <div class="menu-desc text-muted">Update your account password</div>
        </div>
      </a>
    </div>
    
    <!-- Users -->
    <?php if (\Yii::$app->user->can('backend.auth.user.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/auth/user/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-circle-09"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Users Access</div>
          <div class="menu-desc text-muted">List of backend users</div>
        </div>
      </a>
    </div>
    <?php } ?>

    <!-- Assignments -->
    <?php if (\Yii::$app->user->can('backend.auth.userassignment.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/auth/userassignment/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-support-17"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Assignments</div>
          <div class="menu-desc text-muted">Navigate to user/member</div>
        </div>
      </a>
    </div>
    <?php } ?>

    <!-- Role -->
    <?php if (\Yii::$app->user->can('backend.auth.rbac.role') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/auth/rbac/role" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-settings-gear-64"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Role</div>
          <div class="menu-desc text-muted">A collection of permissions</div>
        </div>
      </a>
    </div>
    <?php } ?>

    <!-- Permission -->
    <?php if (\Yii::$app->user->can('backend.auth.rbac.permission') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/auth/rbac/permission" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-lock-circle-open"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Permission</div>
          <div class="menu-desc text-muted">Specific actions a user is allowed to perform</div>
        </div>
      </a>
    </div>
    <?php } ?>

    <!-- members -->
    <?php if (\Yii::$app->user->can('backend.auth.member.index') || \Yii::$app->user->can("root")) { ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
      <a href="<?=$baseUrl ?>/auth/member/index" class="text-decoration-none">
        <div class="card menu-card text-center p-4 h-100">
          <div class="menu-icon mb-2">
            <i class="nc-icon nc-circle-09"></i>
          </div>
          <div class="menu-title fw-bold text-primary">Members Access</div>
          <div class="menu-desc text-muted">List of frontend/client users</div>
        </div>
      </a>
    </div>
    <?php } ?>

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
      success: function (response) {
          form.find('.invalid-feedback').remove();
          form.find('.is-invalid').removeClass('is-invalid');

          if (response.success) {
            $('#changePasswordModal').modal('hide');
            form[0].reset();
            $('.flash-message').html(
              '<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">' +
                response.message +
              '</div>'
            );
          } else {
            // Biarkan ActiveForm render ulang
            // console.log('Response errors:', response.errors);

              // $.each(response.errors, function (key, messages) {
              //   var input = form.find('[name="ChangePasswordUser[' + key + ']"]');
              //   if (input.length) {
              //     input.addClass('is-invalid');

              //     var container = input.closest('.field-changepassworduser-' + key);
              //     var hasFeedback = container.find('.invalid-feedback').length > 0;

              //     if (!hasFeedback) {
              //       container.append('<div class="invalid-feedback">' + messages[0] + '</div>');
              //     }
              //   }
              // });
          }
        },
      error: function (xhr) {
        console.error('AJAX Error:', xhr);
        alert('Terjadi kesalahan AJAX. Cek console untuk detailnya.');
      }
    });
  });
}

$(document).on('click', '.changepassword-user', function() {
    var url = $(this).data('url');
    $('#changePasswordModal .modal-body').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
    $('#changePasswordModal').modal('show');

    $.get(url, function(response) {
        $('#changePasswordModal .modal-body').html(response);
        bindFormAjax();
    });
});



JS;
$this->registerJs($script);
?>
