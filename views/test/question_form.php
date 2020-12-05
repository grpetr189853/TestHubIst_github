<?php

use vova07\imperavi\Widget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form app\components\DynamicTabularForm\DynamicTabularForm */
/* @var $model app\models\Question */
/* @var $questionNumber integer */

?>
<script>
  eval('var questionPreviewContainer_'.concat(<?= $key ?>, '=' ,'"question-preview-container-<?= $key ?>";'));
  eval('var questionRedactorDetach_'.concat(<?= $key ?>,';'));
</script>

<?php 
$rowId = "question-" . $key;

$answerOptionNumber = 0;

$scenario = $model->scenario;

?>

<div class='row-fluid' id="<?php echo $rowId ?>">

    <?php echo $form->field($model, "[$key]id")->hiddenInput()->label(false);?>
    <?php echo $form->updateTypeField($model, $key, "updateType", array('key' => $key));?>


    <div class="question-header">
      <div class="delete-question-button" data-delete = <?= $rowId ?> data-key = <?= $key ?>>
        <i class="fa fa-times fa-2x"></i>
      </div>
      <div class="question-counter" id="question-key-<?= $key ?>">
        <a id="Question<?= $questionNumber ?>">Вопрос №<?= $questionNumber ?></a>
      </div>
    </div>
    <div class="row"  onkeyup="questionPreview<?= $key ?>.Update()">
        <?php echo $form->field($model,"[$key]title")->widget(Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/question/image-upload']),
                'plugins' => [
                    'clips',
                    'fullscreen',
                    'imagemanager',
                ],
            ],
        ])->label("Текст вопроса")?>
	</div>
	<div class="question-preview-container-<?= $key ?>" style="visibility:hidden; position:absolute; top:0; left: 0">
	    <div class="question-preview question-preview-<?= $key ?> process-mathjax"></div>
        <div class="question-buffer question-buffer-<?= $key ?> process-mathjax" style="position:absolute; top:0; left: 0"></div>
	</div>

	<div class="row">
        <?php echo $form->field($model,"[$key]difficulty")->textInput(array('class' => 'questionField'))->label("Количество баллов за ответ"); ?>
	</div>
	
	<?php if($scenario === 'select'):?>
    <div class="js-options-<?= $key ?>">
    <?php foreach($model->optionsNumber as $i): ?>
        <?php $answerOptionNumber++;?>
	    <div class="row answer-option-<?= $i ?>">
	      <div class="answer-option-number-<?= $i ?>"><?= $answerOptionNumber ?>)</div>
            <?php echo $form->field($model, "[$key]answerOptionsArray[{$i}]")->textInput(array('class' => "answer-text-area-{$key}-{$i} questionField", 'onkeyup' => "optionPreview{$key}{$i}.Update()"))->label('Вариант ответа'); ?>
	      <ul class="answer-option-bar">
	        <li title="Показать формулы" data-option-number="<?= $i ?>" data-question-number="<?= $key ?>" onclick="showAnswerOptionPreview(this)">
	          <i class="show-math-button fa fa-superscript"></i>
	        </li>
	        <li title="Удалить" data-option-number="<?= $i ?>" data-question-number="<?= $key ?>" onclick="deleteOption(this)" class="delete-answer-option">
	          <i class="deleteAnswerOption far fa-times-circle fa-2x"></i>
	        </li>
	      </ul>
	      <div class="option-preview-container-<?= $key ?>-<?= $i ?> options-preview-container" style="visibility:hidden; position:absolute; top:0; left: 0">
	        <div class="answer-option-preview answer-option-preview-<?= $key ?>-<?= $i ?> process-mathjax"></div>
            <div class="answer-option-buffer answer-option-buffer-<?= $key ?>-<?= $i ?> process-mathjax" style="position:absolute; top:0; left: 0"></div>
	      </div>
	      <script>
	    	  var optionNumber = <?= $i ?>;
	    	  eval("var optionPreview<?= $key ?>"+optionNumber+"=new Preview('answer-option-preview-<?= $key ?>-'+optionNumber,'answer-option-buffer-<?= $key ?>-'+optionNumber,'answer-text-area-<?= $key ?>-'+optionNumber);");
	    	  eval("optionPreview<?= $key ?>"+optionNumber+".callback=MathJax.Callback(['CreatePreview',optionPreview<?= $key ?>"+optionNumber+"]);");
	    	  eval("optionPreview<?= $key ?>"+optionNumber+".callback.autoReset=true;");

	    	  if($('.answer-option-preview-<?= $key ?>-'+optionNumber).is(':empty')) {
	    		  eval("optionPreview<?= $key ?>"+optionNumber+".Update();");
	    	  }
	      </script>
	    </div>
	<?php endforeach;?>
	</div>
	<div class="addAnswerOption" data-add="js-options-<?= $key ?>" onclick="addOption(this)">
	  <i class="fa fa-plus-square fa-3x"></i>
	</div>
	
    <div class="row">
        <?php echo $form->field($model, "[$key]correctAnswers")->textInput( array('class' => 'questionField'))->label("Номера правильных ответов через запятую");?>
    </div>
    <?php endif;?>

    <?php if($scenario === 'string'):?>
    <div class="row">
        <?php echo $form->field($model,"[$key]answer_text")->textInput(array('size'=>50,'maxlength'=>50, 'class' => 'questionField')); ?>
    </div>
    <?php endif;?>

    <?php if($scenario === 'numeric'):?>
    <div class="row">
        <?php echo $form->field($model,"[$key]answer_number")->textInput(array('size'=>9,'maxlength'=>9, 'class' => 'questionField')); ?>
    </div>

    <div class="row">
        <?php echo $form->field($model,"[$key]precision_percent")->textInput(array('class' => 'questionField')); ?>
    </div>
    <?php endif;?>
    
<?php echo $form->field($model,"[$key]modelScenario")->hiddenInput(array('value' => $scenario, 'class' => 'js-question-type'))->label(false); ?>
</div>