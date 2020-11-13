<?php

namespace app\components\DynamicTabularForm;

use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

class DynamicTabularForm extends ActiveForm
{

    const UPDATE_TYPE_CREATE = 'create';

    const UPDATE_TYPE_DELETE = 'delete';

    const UPDATE_TYPE_UPDATE = 'update';

    public $rowUrl;
    
    public $defaultRowView = 'question_from';

    public $rowViewCounter = 0;

    public function init()
    {
        parent::init();
        if ($this->rowUrl == null)
            $this->rowUrl = Url::to('getRowForm');
    }
    
    public function updateTypeField($model, $key, $attribute, $htmlOptions = array()) {
        if ($model->isNewRecord) {
            $model->{$attribute} = self::UPDATE_TYPE_CREATE;
        } else {
            $model->{$attribute} = self::UPDATE_TYPE_UPDATE;
        }

        $htmlOptions = array_merge($htmlOptions, array('id' => get_class($model) . '_upateType_' . $htmlOptions['key'], 'class' => 'update-type-field'));
    
//        return parent::hiddenField($model, "[$key]".$attribute, $htmlOptions);
        return parent::field($model, "[$key]".$attribute)->hiddenInput()->label(false);
    }

    public function myRow($models = array(), $rowView = null)
    {
        if ($rowView == null) {
            $rowView = $this->defaultRowView;
        }
        
        $buttonId = 'addButton-' . $this->rowViewCounter;
        $content = '';
//        Model::validateMultiple($models);
        foreach ($models as $key => $model) {
            $content .=  \Yii::$app->controller->renderPartial($rowView, array(
                'key' => $key+1,
                'questionNumber' => $key+1,
                'model' => $model,
                'form' => $this
            ));
        }
        echo Html::tag('div',$content, array('class' => 'question-forms'));

        echo "</div>";
        $question_creator_content =  Html::dropDownList('scenario', 'empty', array(
            'empty'=>'Выберите тип ответа на вопрос',
            'select' => 'Ответ из перечисленных вариантов',
            'string' => 'Ответ строкой',
            'numeric' => 'Ответ числом'
        ),['class' => 'question-drop-list', 'id' => 'scenario-drop-list']);
        echo Html::tag('div', $question_creator_content , array('class' => 'question-creator'));
        /*
        echo CHtml::openTag('div', array('class' => 'create-question-header'));
        echo 'Добавить вопрос';
        echo "</div>";
        */


        /*
        echo CHtml::button('', array(
            'id' => $buttonId,
            'class' => 'add-question-button fa fa-plus-square-o'
        ));
        */
        echo "<div class='add-question-button far fa-plus-square fa-3x' id={$buttonId} title='Добавить вопрос'></div>";
        
        echo "</div>";
        
//        $cs = Yii::app()->clientScript;
        $view = $this->getView();
        $view->registerJs("
            var counter = " . count($models) . ";
            var questionNumber = counter;
            
            function addRow(scenario){
                counter = counter + 1;
                questionNumber = questionNumber + 1;
                $.ajax({
                    url:'" . $this->rowUrl . "',
                    data:{
                        key:counter,
                        questionNumber:questionNumber,
                        scenario:scenario
                    },
                    success:function(data){appendRow(data)},
                });
            }
            function appendRow(html){
               $('.question-forms').append(html);
            }
            
            //for adding rows
            $('#" . $buttonId . "').click(function(e){
              var scenario = $('#scenario-drop-list').val(); 
              if(scenario === 'empty') {
                $('.question-drop-list').addClass('error');
                return false;
              } else {
                $('.qeustions-empty-error').empty();
                $('.qeustions-empty-error').removeClass('alert alert-danger');
                if($('.question-drop-list').hasClass('error')) {
                  $('.question-drop-list').removeClass('error');
                }
              }
              addRow(scenario);
            });
            
            $('.question-forms').on('click','.delete-question-button',function(e) {
               var key = $(this).attr('data-key');
               var rowId = $(this).attr('data-delete');
               var updateTypeField = $('#'+rowId).find('.update-type-field');
            
               if(updateTypeField.val() == '" . self::UPDATE_TYPE_CREATE . "'){
                    $('#'+rowId).remove();
                } else {
                    updateTypeField.val('" . self::UPDATE_TYPE_DELETE . "');
                    $('#'+rowId).hide();
                }
            
               questionNumber = questionNumber - 1;
               var number = $('.row-fluid').size();
            
               var numberId = new Array();
            
               $('.question-counter:visible').each(function () {
                    numberId.push(this.id);
               });
            
               var i=1;
            
               $.each(numberId, function( index, value ) {
                   $('#'+value).empty();
                   $('#'+value).append('Вопрос №'+i);
                   i++;
               });
            });
       
        ");
        $this->rowViewCounter = $this->rowViewCounter + 1;
    }
    
    public function deleteFormButton($rowId, $key, $label = '', $htmlOptions = array())
    { 
        echo Html::button($label, array('class' => 'delete-form-button', 'data-delete' => $rowId, 'data-key' => $key));
    }
}

?>
