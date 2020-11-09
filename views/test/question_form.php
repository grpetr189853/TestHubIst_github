<?php

use yii\helpers\Url;

?>
<script>
  eval('var questionPreviewContainer_'.concat(<?= $key ?>, '=' ,'"question-preview-container-<?= $key ?>";'));
  eval('var questionRedactorDetach_'.concat(<?= $key ?>,';'));
</script>

<?php 
$rowId = "question-" . $key;

$answerOptionNumber = 0;
//$csrfTokenName = Yii::app()->request->csrfTokenName;
//$csrfToken = Yii::app()->request->csrfToken;
$scenario = $model->scenario;

$this->registerJs(
"
    $('.question-text-".$key."').redactor({
        lang: 'ru',
        buttonsHide: ['link'],
        toolbarFixed: false,
        pastePlainText: true,
        imageLink: false,
        imageUpload: '".Url::to('questionImages/tmpUpload')."',
        imageUploadParam: 'QuestionImage[imageFile]',
        pasteCallback: function(html) {
            return html.replace(/<p>(.*?)<\/p>/gi, '$1');
        },
        initCallback: function(){
            var redactorEditor = $('.question-forms').find('div[class=redactor-editor]');
            redactorEditor.addClass('redactor-editor-question redactor-editor-".$key."');
            redactorEditor.attr('data-question-number','".$key."');
        },
        imageUploadErrorCallback: function(json){
            alert(json.message);
        },
        imageDeleteCallback: function(url, image){
            $.ajax({
                url:'" . Url::to('questionImages/deleteTmpImage') . "',
                type: 'POST',
                data: {
                    url:url,

                },
            });
        },
        uploadImageFields: {

        },
        uploadFileFields: {

        },
        plugins: ['viewTextarea']
    });
", \yii\web\View::POS_END);

?>

<div class='row-fluid' id="<?php echo $rowId ?>">
<!--    --><?php //echo $form->hiddenField($model, "[$key]id");?>
<!--    --><?php //foreach ($model->getErrors() as $error):?>
<!--        --><?//= $error[0];?>
<!--    --><?php //endforeach; ?>

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
<!--		--><?php //echo $form->labelEx($model,"title"); ?>
<!--		--><?php //echo $form->textArea($model,"[$key]title",array('rows'=>6, 'cols'=>50, 'class' => 'questionField question-text-'.$key)); ?>
<!--		--><?php //echo $form->error($model,"[$key]title", array(), false, false); ?>
        <?php echo $form->field($model,"[$key]title")->textarea(array('rows'=>6, 'cols'=>50, 'class' => 'questionField question-text-'.$key))?>
	</div>
	<div class="question-preview-container-<?= $key ?>" style="visibility:hidden; position:absolute; top:0; left: 0">
	    <div class="question-preview question-preview-<?= $key ?> process-mathjax"></div>
        <div class="question-buffer question-buffer-<?= $key ?> process-mathjax" style="position:absolute; top:0; left: 0"></div>
	</div>

	<div class="row">
<!--		--><?php //echo $form->labelEx($model,"difficulty"); ?>
<!--		--><?php //echo $form->textField($model,"[$key]difficulty", array('class' => 'questionField')); ?>
<!--		--><?php //echo $form->error($model,"[$key]difficulty", array(), false, false); ?>
        <?php echo $form->field($model,"[$key]difficulty")->textInput(array('class' => 'questionField')); ?>
	</div>
	
	<?php if($scenario === 'select'):?>
    <div class="js-options-<?= $key ?>">
<!--    --><?php //echo $form->labelEx($model,"answerOptionsArray"); ?>
    <?php foreach($model->optionsNumber as $i): ?>
        <?php $answerOptionNumber++;?>
	    <div class="row answer-option-<?= $i ?>">
	      <div class="answer-option-number-<?= $i ?>"><?= $answerOptionNumber ?>)</div>
<!--	      --><?php //echo $form->textField($model, "[$key]answerOptionsArray[{$i}]", array('class' => "answer-text-area-{$key}-{$i} questionField", 'onkeyup' => "optionPreview{$key}{$i}.Update()")); ?>
          <?php echo $form->field($model, "[$key]answerOptionsArray[{$i}]")->textInput(array('class' => "answer-text-area-{$key}-{$i} questionField", 'onkeyup' => "optionPreview{$key}{$i}.Update()")); ?>
	      <ul class="answer-option-bar">
	        <li title="Показать формулы" data-option-number="<?= $i ?>" data-question-number="<?= $key ?>" onclick="showAnswerOptionPreview(this)">
	          <i class="show-math-button fa fa-superscript"></i>
	        </li>
	        <li title="Удалить" data-option-number="<?= $i ?>" data-question-number="<?= $key ?>" onclick="deleteOption(this)">
	          <i class="deleteAnswerOption fa fa-times-circle-o fa-2x"></i>
	        </li>
	      </ul>
<!--	      --><?php //echo $form->error($model, "[$key]answerOptionsArray[{$i}]", array('class' => 'errorMessage answerOptionError'), false, false); ?>
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
<!--	    --><?php //echo $form->labelEx($model,"correctAnswers"); ?>
<!--	    <em>Укажите номера правильных ответов через зяпятую, если их несколько</em>-->
<!--	    --><?php //echo $form->textField($model, "[$key]correctAnswers", array('class' => 'questionField')); ?>
<!--	    --><?php //echo $form->error($model,"[$key]correctAnswers", array(), false, false); ?>
        <?php echo $form->field($model, "[$key]correctAnswers")->textInput( array('class' => 'questionField'));?>
    </div>
    <?php endif;?>

    <?php if($scenario === 'string'):?>
    <div class="row">
<!--	    --><?php //echo $form->labelEx($model,"answer_text"); ?>
<!--	    <em>Правильный ответ в виде строки</em>-->
<!--	    --><?php //echo $form->textField($model,"[$key]answer_text",array('size'=>50,'maxlength'=>50, 'class' => 'questionField')); ?>
<!--	    --><?php //echo $form->error($model,"[$key]answer_text", array(), false, false); ?>
        <?php echo $form->field($model,"[$key]answer_text")->textInput(array('size'=>50,'maxlength'=>50, 'class' => 'questionField')); ?>
    </div>
    <?php endif;?>

    <?php if($scenario === 'numeric'):?>
    <div class="row">
<!--	    --><?php //echo $form->labelEx($model,"answer_number"); ?>
<!--	    <em>Правильный ответ в виде числа</em>-->
<!--	    --><?php //echo $form->textField($model,"[$key]answer_number",array('size'=>9,'maxlength'=>9, 'class' => 'questionField')); ?>
<!--	    --><?php //echo $form->error($model,"[$key]answer_number", array(), false, false); ?>
        <?php echo $form->field($model,"[$key]answer_number")->textInput(array('size'=>9,'maxlength'=>9, 'class' => 'questionField')); ?>
    </div>

    <div class="row">
<!--	    --><?php //echo $form->labelEx($model,"Погрешность в процентах"); ?>
<!--	    <em>Если необходимо, укажите погрешность ответа в процентах</em>-->
<!--	    --><?php //echo $form->textField($model,"[$key]precision_percent", array('class' => 'questionField')); ?>
<!--	    --><?php //echo $form->error($model,"[$key]precision_percent", array(), false, false); ?>
        <?php echo $form->field($model,"[$key]precision_percent")->textInput(array('class' => 'questionField')); ?>
    </div>
    <?php endif;?>
    
<!--    --><?php //echo $form->hiddenField($model,"[$key]modelScenario", array('value' => $scenario, 'class' => 'js-question-type')); ?>
<?php echo $form->field($model,"[$key]modelScenario")->hiddenInput(array('value' => $scenario, 'class' => 'js-question-type'))->label(false); ?>
</div>

<script>
$('.row-fluid').parent().addClass('ignore-mathjax');

var counter = <?= $key?>;
eval("var questionPreview"+counter+"=new Preview('question-preview-<?= $key ?>','question-buffer-<?= $key ?>','redactor-editor-<?= $key?>');");
eval("questionPreview"+counter+".callback=MathJax.Callback(['CreatePreview',questionPreview"+counter+"]);");
eval("questionPreview"+counter+".callback.autoReset=true;");

if($('.question-preview-'+counter).is(':empty')) {
	eval("questionPreview"+counter+".Update();");
}
</script>