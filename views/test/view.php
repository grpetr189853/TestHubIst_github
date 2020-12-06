<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Test */
/* @var $foreword string */
/* @var $category string */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="test-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(User::isUserAdmin(\Yii::$app->user->identity->username)||User::isUserTeacher(\Yii::$app->user->identity->username)):?>
    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php elseif (User::isUserStudent(\Yii::$app->user->identity->username)): ?>
        <p>
            <?= Html::a('Начать тест', ['test/init', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'name',
            ['label'=>'Foreword','value'=> html_entity_decode($foreword)],
            ['label'=> 'Category','value'=> $category],
            'minimum_score',
            'time_limit',
            'attempts',
            'create_time',
            'deadline',
        ],
    ]) ?>

</div>
