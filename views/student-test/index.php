<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мои тесты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-test-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="student-test-status-nav-wrapper">
        <?= Html::a('Не пройденные', ['index','status'=> 'notpassed'], ['class' => 'student-test-status-nav']) ?>
        <?= Html::a('Проваленные', ['index','status'=> 'failed'], ['class' => 'student-test-status-nav']) ?>
        <?= Html::a('Выполненные', ['index','status'=> 'completed'], ['class' => 'student-test-status-nav']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'attempts',
            'deadline',
            'result',
            'test_id',
            //'student_id',
            //'start_time',
            //'end_time',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{pass}',  // the default buttons + your custom button
                'buttons' => [
                    'pass' => function($url, $model, $key) {     // render your custom button
                        return Html::a('Pass Test', ['test/view', 'id' => $model->test_id]);
                    },
                ],
            ]
        ],
    ]); ?>


</div>
