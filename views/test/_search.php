<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="test-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'foreword') ?>

    <?= $form->field($model, 'category_id') ?>

    <?= $form->field($model, 'minimum_score') ?>

    <?php // echo $form->field($model, 'time_limit') ?>

    <?php // echo $form->field($model, 'attempts') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'deadline') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
