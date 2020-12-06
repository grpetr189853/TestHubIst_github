<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">

    <div class="row">
        <div class="col-lg-4">
            <p>Введите свое имя пользователя и пароль, указанный при регистрации:</p>
        </div>
    </div>

    <?php $form = ActiveForm::begin([
        'layout'=>'horizontal',
        'options' => ['class' => 'signup-form form-register1'],
        'fieldConfig' => [
            'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-4',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

        <div class="row">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Ваше имя', 'class'=>'form-control text-center']) ?>
        </div>

        <div class="row">`
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Ваше пароль', 'class'=>'form-control text-center']) ?>
        </div>

        <div class="row rememberMe">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]) ?>
        </div>
        <div class="row forgot">
            <div class="form-group field-forgot text-left">
                <div style="color:#999;/*margin:1em 0;*/display: flex;" class="col-lg-offset-1 col-lg-3 text-center">
                    <?= Html::a('Забыли пароль?', ['site/request-password-reset']) ?>.
                </div>
                <div class="div col-lg-8">

                </div>
            </div>
        </div>
        <div class="row buttons">
            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Войти', ['class' => 'login-button', 'name' => 'login-button']) ?>
                </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>
