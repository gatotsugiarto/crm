<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Login';
?>

<style>
body{
    margin:0;
    font-family: Arial, Helvetica, sans-serif;
}

/* Background biru */
.login-page{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#ffffff;
}

/* Card */
.login-card{
    width:320px;
    padding:30px;
    border-radius:10px;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
    text-align:center;
}

.login-card h2{
    margin-bottom:20px;
    color:#333;
}

/* Form */
.form-group{
    text-align:left;
    margin-bottom:15px;
}

.form-group label{
    font-size:13px;
    font-weight:600;
}

.form-control{
    width:100%;
    padding:9px;
    margin-top:5px;
    border:1px solid #d0d7e2;
    border-radius:6px;
    font-size:14px;
}

/* Remember */
.form-remember{
    text-align:left;
    margin-bottom:15px;
    font-size:13px;
}

/* Button */
.btn-login{
    width:100%;
    padding:10px;
    background:#0d6efd;
    border:none;
    color:#fff;
    font-weight:600;
    border-radius:6px;
    cursor:pointer;
    transition:0.2s;
}

.btn-login:hover{
    background:#0b5ed7;
}

.password-wrapper{
    position:relative;
}

.password-wrapper .form-control{
    padding-right:35px;
}

.toggle-password{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#888;
    font-size:14px;
}

.toggle-password:hover{
    color:#0d6efd;
}

.login-icon{
    width:70px;
    height:70px;
    margin:0 auto 10px auto;
    border-radius:50%;
    background:#0d6efd;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:28px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

</style>

<div class="login-page">
    <div class="login-card">

        <div class="login-icon">
            <i class="fa-solid fa-user-lock"></i>
        </div>

        <h5>User Sign In</h5>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <div class="form-group">
                <label>Username</label>
                <?= $form->field($model, 'username')->textInput([
                    'placeholder' => 'Enter username',
                    'class' => 'form-control',
                ])->label(false) ?>
            </div>

            <div class="form-group password-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <?= $form->field($model, 'password', [
                        'template' => '
                            {label}
                            <div class="password-wrapper">
                                {input}
                                <span class="toggle-password" onclick="togglePassword()">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </div>
                            {error}
                        ',
                    ])->passwordInput([
                        'id' => 'password-input',
                        'class' => 'form-control',
                        'placeholder' => 'Enter password'
                    ])->label(false) ?>
                </div>
            </div>

            <div class="form-remember">
                <label>
                    <input type="checkbox"> Remember Me
                </label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        <?php ActiveForm::end(); ?>
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
