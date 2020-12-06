<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Назначить тест пользователю';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-list">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['id'=>'assign-table', "class" => "table table-striped table-bordered"],
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            'email',
            [
            'attribute' => 'status',
            'filter' => app\components\helpers\UserStatusHelper::statusList(),
                'value' => function (app\models\User $model) {
                    return app\components\helpers\UserStatusHelper::statusLabel($model->status);
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->id];
                }
            ],
        ],
    ]); ?>
<?php foreach ($studentTestArray as $studentTest){
            $this->registerJs("
                $('input:checkbox').each(function(e){
                    if($(this).val() == '". $studentTest->student_id ."'){
                        $(this).prop('checked', true);;                       
                    } 
                });
            ");
      }
 ?>

<?php $this->registerJs("
$('input:checkbox').each(function(e){
    $(this).addClass(\"assign-checkbox-\"+e);
});

//function to get url parameters
function getUrlParams(url = location.search){
  // var regex = /[/&]([^=#]+)=([^&#]*)/g, params = {}, match;
  // while(match = regex.exec(url)) {
  //     params[match[1]] = match[2];
  // }
  // return params;
  return url.substr(url.lastIndexOf('/') + 1);
}
$(\"body\").on(\"click\",\"#assign-table\", function(e){
       e.preventDefault();
       var checkboxArr = [];
       $('[class ^= assign-checkbox-]').each(function(e){
            checkboxArr.push($(this).attr('class'));
       })
       if(checkboxArr.includes(e.target.className)){
//           var keys = $('#w0').yiiGridView(\"getSelectedRows\");
           var keys = $($(\"input.\" + e.target.className)[0]).val();
           var test_id = getUrlParams(location.href);
           var url = '". \yii\helpers\Url::toRoute(['test/assign-test'])."' + \"/id/\" + test_id.id;  
           var doDelete;
           if($($(\"input.\" + e.target.className)[0]).is(':checked')){
                doDelete = false;           
           } else {
                doDelete = true;
           }
           $.ajax({
             url: url,
             type: \"POST\",
             data: {student_id: keys, doDelete: doDelete},
             success: function(){
                if(doDelete){
                    $(\"input.\" + e.target.className)[0].checked = false;                            
                }else{
                    $(\"input.\" + e.target.className)[0].checked = true;            
                }
             }
           })
       }
   });
"); ?>

</div>