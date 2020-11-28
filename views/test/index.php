<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Test', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'foreword',
            'category_id',
            'minimum_score',
            //'time_limit:datetime',
            //'attempts',
            //'create_time',
            //'deadline',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {assign}',  // the default buttons + your custom button
                'buttons' => [
                    'assign' => function($url, $model, $key) {     // render your custom button
                        return Html::a('Assign', ['test/assign-test', 'id' => $model->id]);
                    }
                ]

            ],
        ],
    ]); ?>


</div>
