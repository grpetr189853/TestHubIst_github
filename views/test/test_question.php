<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $answerModel \app\models\StudentAnswer */
/* @var $testId integer */
/* @var $questionNumber integer */
/* @var $questionNumberIdPair array */
/* @var $questionDataArray array */
/* @var $numberOfQuestions integer */
/* @var $studentAnswersQuestionId array */
/* @var $testTimeLimit integer */
/* @var $testStartTime \DateTime*/

if(!empty($questionDataArray)) {
    $i = $questionNumber;

    foreach ($questionNumberIdPair as $number => $questionId) {
        if ($i + 1 > $numberOfQuestions) {
            $nextQuestionNumber = null;
            break;
        }

        if (!in_array($questionNumberIdPair[$i + 1], $studentAnswersQuestionId)) {
            $nextQuestionNumber = $i + 1;
            break;
        }
        $i++;
    }

    if ($nextQuestionNumber === null) {
        $nextQuestionNumber = 'end';
    }

    $form = ActiveForm::begin([
        'id' => 'answer-form',
        'action' => ['test/process', 'id' => $testID, 'q' => $questionNumber + 1],
        'options'=>['class' => 'form-horizontal']
    ]);

//    $questionDataArray['answerIdTextPair'] = array_map(function ($string) {
//        return Html::encode($string);
//    }, $questionDataArray['answerIdTextPair']);

    $htmlOptions = array(
        'class' => 'answer-radio-button',
        'separator' => ' ',
        'template' => "{beginLabel} {input} <span class='option-number'></span> {labelTitle} {endLabel}",
        'container' => "div class='question-choices'",
        'labelOptions' => array('class' => 'question-choice')
    );

    $this->registerJs('
           var answeredQuestion = ' . JSON::encode($studentAnswersQuestionId) . ';
           $.each(answeredQuestion, function(key, questionID) {
               $("#question-anchor-"+questionID).css("background", "#eee");
           });    
    ');

    switch($questionDataArray['type']){
        case 'select_many':
            $questionAnswer = $form->field($answerModel, 'selectedAnswers')->checkboxList(array_map(function($v){return trim(strip_tags($v));},$questionDataArray['answerIdTextPair']),
                )->label(false);
            break;
        case 'select_one':
            $questionAnswer = $form->field($answerModel, 'answer_id')->radioList(array_map(function($v){return trim(strip_tags($v));},$questionDataArray['answerIdTextPair']),
                )->label(false);
            break;
        case 'string':
            $questionAnswer = $form->field($answerModel,'answer_text',['inputOptions' => [
                'autocomplete' => 'off']])->textInput()->label(false);
            break;
        case 'numeric':
            $questionAnswer = $form->field($answerModel, 'answer_number')->textInput(['type' => 'number']);
            break;
    }

    if($questionDataArray['type'] == 'select_many' || $questionDataArray['type'] == 'select_one') {
        $i=0;
        foreach($questionDataArray['answerIdTextPair'] as $value) {
            $i++;
            $questionAnswer = preg_replace("{(<span class='option-number'>)(<\/span>)}ui", "$1 {$i} $2", $questionAnswer, 1);
        }
    }
}


?>
<?php if(empty($questionAlert)):?>
<div class="test-question-counter">
    <span><?= $questionNumber ?> из <?= $numberOfQuestions ?></span>
</div>
<div class='question-number'>
    Вопрос №<?= $questionNumber ?>:
</div>
<div class='question-text'>
    <p>
        <?= $questionDataArray['title'] ?>
    </p>
</div>
<div class='question-answer'>
    <?= $questionAnswer ?>
</div>
<?= $form->field($answerModel, 'question_id')->hiddenInput(array('value' => $questionDataArray['id']))->label(false); ?>
<?= $form->field($answerModel, 'questionNumber')->hiddenInput(array('value' => $questionNumber))->label(false); ?>
<?= $form->field($answerModel, 'scenario')->hiddenInput(array('value' => $questionDataArray['type']))->label(false); ?>
<?= Html::hiddenInput('nextQuestionNumber', $nextQuestionNumber,['id' => 'nextQuestionNumber']); ?>
<?= Html::hiddenInput('testId', $testID); ?>
<?= Html::hiddenInput('testTimeLimit', $testTimeLimit); ?>
<?= Html::hiddenInput('testStartTime', $testStartTime); ?>
<div class="answer-buttons-container">
        <?= Html::submitButton('Ответить', ['id'=>'btn-submit','class' => 'submit-answer-button']) ?>
</div>
<?php
    $this->registerJs('
jQuery(\'body\').on(\'click\',\'#btn-submit\',function(){console.log(jQuery(this).parents("form"));jQuery.ajax({\'type\':\'POST\',\'complete\':function(e) {
                  var questionNumber = $("#StudentAnswer_questionNumber").val();
                  var nextQuestionNumber = $("#nextQuestionNumber").val();
                  var url = "'. Url::to(['test/process','id' => $testID]) .'"+"/"+nextQuestionNumber;
                  var response = JSON.parse(e.responseText);
                  if(response.hasOwnProperty("redirect")) {
                      window.location.replace("http://testhub_yii" + response.redirect);
                  }
        
                  if(!response.hasOwnProperty("validateStatus")){
                      $.each(response, function(key, val) {
                          //$(".err").text(val);
                          $("#answer-form #"+key+"_em_").text(val);
                          $("#answer-form #"+key+"_em_").css("display", "block");
                      });
                  } else {
                      $(".question-anchor-"+questionNumber).css("background", "#eee");
                      swapQuestion(url);
                      history.pushState(null, null, url);
                  }
              },\'error\':function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            console.log(err.Message);
        },\'url\':"'. Url::to(['test/process','id' => $testID, 'q' => $nextQuestionNumber]).'",\'cache\':false,\'data\':jQuery(this).parents("form").serialize()});return false;});

');
?>
<?php ActiveForm::end(); ?>
<?php endif;?>

<?php if(!empty($questionAlert)):?>
    <div class="question-alert">
        <p><?= $questionAlert ?></p>
        <?php echo Html::beginForm(['test/result', 'id' => $testID], 'post');?>
        <?php echo Html::hiddenInput('endTest', true) ?>
        <?php echo Html::submitButton('Завершить', array('class'=>'end-test-button')) ?>
        <?php echo Html::endForm(); ?>
    </div>
<?php endif;?>
