<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Login';
?>

<div class="login-page">
    <div class="card shadow-lg border-0 rounded-4 login-card">
        <div class="login-card-header text-white text-center rounded-top">
            <h3 class="fw-bold mb-0"><i class="fa fa-lock me-2"></i> &nbsp;<?= Html::encode($this->title) ?></h3>
        </div>
        <div class="card-body p-4">
            <p class="text-muted text-center mb-4">
                Please enter your credentials to access the dashboard.
            </p>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true,
                'placeholder' => 'Username',
                'class' => 'form-control rounded-pill'
            ]) ?>

            <div class="position-relative mb-3">
                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => 'Password',
                    'class' => 'form-control rounded-pill',
                    'id' => 'password-input'
                ])->label(false) ?>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa fa-eye"></i>
                </span>
            </div>

            <div class="form-check mb-3">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" id="rememberMe" name="LoginForm[rememberMe]" value="1">
                <span class="form-check-sign"></span>
                Remember Me
              </label>
            </div>


            <div class="d-grid">
                <?= Html::submitButton('Login', [
                    'class' => 'btn btn-warning btn-lg rounded-pill shadow-sm fw-bold',
                    'name' => 'login-button'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password-input');
    const icon = document.querySelector('.toggle-password i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>