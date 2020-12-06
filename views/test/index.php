<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тесты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
//            'foreword',
//            'category_id',
            'minimum_score',
            //'time_limit:datetime',
            'attempts',
            //'create_time',
            'deadline',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {assign} {statistic}',  // the default buttons + your custom button
                'buttons' => [
                    'assign' => function($url, $model, $key) {     // render your custom button
                        return Html::a('<i class="fas fa-user-tag fa-2x"></i>', ['test/assign-test', 'id' => $model->id]);
                    },
                    'statistic' => function($url, $model, $key) {     // render your custom button
                        return Html::a('<i class="fas fa-chart-line fa-2x"></i>', ['test/statistic', 'id' => $model->id]);
                    },
                ]

            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('Create Test', ['create'], ['class' => 'general-button new-test-button']) ?>
    </p>
</div>
