<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $role string */

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">

    <p>Заполните следующие поля что бы зарегистрироваться как <?= $role; ?>:</p>
    <div class="row">
        <div class="col-lg-12">

            <?php $form = ActiveForm::begin([
                'id' => 'form-signup',
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
            <div class="row">
                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Ваше email', 'class'=>'form-control text-center']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Ваше имя', 'class'=>'form-control text-center']) ?>
            </div>
            <div class="row buttons">
                <div class="form-group col-sm-4 text-center">
                    <?= Html::submitButton('Зарегистрироваться', ['class' => 'signup-button', 'name' => 'signup-button']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
