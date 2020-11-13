<?php

use app\components\DynamicTabularForm\DynamicTabularForm;
use vova07\imperavi\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$form = DynamicTabularForm::begin(array(
        'defaultRowView'=>'question_form',
        'id'=>'test-form',
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>true,
    ));
//    $errorModels = $questions;
//    array_unshift($errorModels, $test);
    //var_dump(strtotime('2015-02-15 21:00:00'), $test->deadline);
?>

<?php //echo $form->errorSummary([$test, $questions]); ?>

<div class="form th-test-from ignore-mathjax">

    <div class="test-fields">
<!--    --><?php //echo $form->errorSummary($errorModels, '<p>Пожалуйста, исправьте следующие ошибки:</p>'); ?>
    <div class="row">
<!--		--><?php //echo $form->labelEx($test,'name'); ?>
<!--		--><?php //echo $form->textField($test,'name',array('size'=>45,'maxlength'=>255, 'class' => 'test-name-field')); ?>
<!--		--><?php //echo $form->error($test,'name'); ?>
        <?= $form->field($test,'name')->textInput() ?>
	</div>
    
    
    
<!--	<div class="row" onkeyup="forewordPreview.Update()">-->
        <div class="row">
<!--		--><?php //echo $form->labelEx($test,'foreword'); ?>
<!--		--><?php //echo $form->textArea($test,'foreword',array('rows'=>10, 'cols'=>70, 'class'=>'foreword-redactor')); ?>
<!--		--><?php //echo $form->error($test,'foreword'); ?>
        <?= $form->field($test,'foreword')->widget(Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/test/image-upload']),
                'plugins' => [
                    'clips',
                    'fullscreen',
                    'imagemanager',
                ],
            ],
        ])?>
	</div>
	<div class="foreword-preview-container" style="visibility:hidden; position:absolute; top:0; left: 0">
	    <div class="foreword-preview process-mathjax"></div>
        <div class="foreword-buffer process-mathjax"></div>
	</div>

   <div class="row">
       <?= $form->field($test, 'category_id')->dropDownList(ArrayHelper::map(\app\models\TestsCategory::find()->all(), 'id', 'name'))->label(false); ?>
   </div>

	<div class="row">
<!--		--><?php //echo $form->labelEx($test,'minimum_score'); ?>
<!--		<em>Минимальное количество баллов, необходимых для прохождения теста</em>-->
<!--		--><?php //echo $form->textField($test,'minimum_score'); ?>
<!--		--><?php //echo $form->error($test,'minimum_score'); ?>
        <?= $form->field($test,'minimum_score')->textInput()?>
	</div>

	<div class="row">
<!--		--><?php //echo $form->labelEx($test,'time_limit'); ?>
<!--		<em>Время в минутах за которое студент должен выполнить тест</em>-->
<!--		--><?php //echo $form->textField($test,'time_limit'); ?>
<!--		--><?php //echo $form->error($test,'time_limit'); ?>
        <?= $form->field($test,'time_limit')->textInput()?>
	</div>

	<div class="row">
<!--		--><?php //echo $form->labelEx($test,'attempts'); ?>
<!--		<em>Число попыток сдачи теста</em>-->
<!--		--><?php //echo $form->textField($test,'attempts'); ?>
<!--		--><?php //echo $form->error($test,'attempts'); ?>
        <?= $form->field($test,'attempts')?>
	</div>

	<div class="row">
<!--		--><?php //echo $form->labelEx($test,'deadline'); ?>
<!--		<em>Крайний срок для сдачи теста относительно вашего часового пояса. Формат: гггг-мм-дд чч:мм.</em>-->
		<?php 
		    $dateTimeHtmlOptions = array();
		
		    if(!strtotime($test->deadline) || strtotime($test->deadline) < 0) {
                $dateTimeHtmlOptions = array('value'=>'гггг-мм-дд чч:мм');
            }
		    
//		    echo $form->textField($test,'deadline', $dateTimeHtmlOptions);
            echo $form->field($test,'deadline')->textInput();
		?>
<!--		--><?php //echo $form->error($test,'deadline'); ?>
	</div>
	
	<div class="row">
<!--		--><?php //echo $form->labelEx($test,'testGroups'); ?>
<!--		<em>Укажите группы, для которых предназначен тест. Если групп несколько, можно указать их через запятые/пробелы или же перечислить несколько подряд идущих групп 1050-1053.</em>-->
<!--		--><?php //echo $form->textField($test,'testGroups'); ?>
<!--		--><?php //echo $form->error($test,'testGroups'); ?>
	</div>
	
	</div>
<?php
    echo $form->myRow($questions);
?>

<div class="test-create-wrapper">
<?php echo Html::submitButton($pageLabel, array('class' => 'create-test-button th-submit-button', 'name' => 'submit-button')); ?>
<div class="qeustions-empty-error">

</div>
</div>
<?php DynamicTabularForm::end();  ?>
</div>

<!--<script>-->
<!--var forewordPreview = new Preview('foreword-preview','foreword-buffer','redactor-editor-foreword');-->
<!--forewordPreview.callback = MathJax.Callback(["CreatePreview",forewordPreview]);-->
<!--forewordPreview.callback.autoReset = true;-->
<!---->
<!--if($('.foreword-preview').is(':empty')) {-->
<!--	forewordPreview.Update();-->
<!--}-->
<!---->
<!--</script>-->