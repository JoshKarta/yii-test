<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

// Yii::$app->configuration->clearCache();
$enableForgotPassword = (bool)Yii::$app->configuration->get('enableForgotPassword', false, 'frontend');

// Debug both database value and cached value
// $debugConfig = \common\models\Config::find()
//     ->joinWith(['category'])
//     ->where(['config_category.name' => 'frontend', 'config.key' => 'enableForgotPassword'])
//     ->one();

// echo "Database value: " . ($debugConfig ? ($debugConfig->value ? 'true' : 'false') : 'not found') . "<br>";
// echo "Cached value: " . ($enableForgotPassword ? 'true' : 'false') . "<br>";
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="my-1 mx-0" style="color:#999;">
                <?php if ($enableForgotPassword) : ?>
                    If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>
                <?php endif; ?>
                <br>
                Need new verification email? <?= Html::a('Resend', ['site/resend-verification-email']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>