<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StudentTest */

$this->title = 'Create Student Test';
$this->params['breadcrumbs'][] = ['label' => 'Student Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-test-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
