<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Tests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-test-index">

    <h1><?= Html::encode($this->title) ?></h1>


        <?= Html::a('Not passed', ['index','status'=> 'notpassed'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Not completed', ['index','status'=> 'notcompleted'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Failed', ['index','status'=> 'failed'], ['class' => 'btn btn-danger']) ?>
        <?= Html::a('Completed', ['index','status'=> 'completed'], ['class' => 'btn btn-success']) ?>


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
                'template' => '{view} {update} {delete} {pass}',  // the default buttons + your custom button
                'buttons' => [
                    'pass' => function($url, $model, $key) {     // render your custom button
                        return Html::a('Pass Test', ['test/view', 'id' => $model->test_id]);
                    },
                ],
            ]
        ],
    ]); ?>


</div>
