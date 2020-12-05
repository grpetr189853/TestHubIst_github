<?php

use yii\helpers\Html;

/** @var $model \app\models\Question */
/** @var $number integer */
/** @var $key integer */
?>
<div class="row answer-option-<?= $i ?>" style="display: block;">
    <div class="answer-option-number-<?= $i ?>" style="display: inline-block"><?= $number ?>)</div>
    <?= Html::activeLabel($model, "[$key]answerOptionsArray[{$i}]", ['label' => 'Вариант ответа','style' => 'display: inline-block']); ?>
    <?php echo Html::activeTextInput($model, "[$key]answerOptionsArray[{$i}]", array('rows'=>2, 'cols'=>30, 'class' => "answer-text-area-{$key}-{$i} questionField", 'id' => "'question-{$key}-answeroptionsarray-{$i}'", 'style' =>'display: block; margin-left: 3%; width: 15%', 'onkeyup' => "optionPreview{$key}{$i}.Update()")); ?>
    <ul class="answer-option-bar">
        <li title="Показать формулы" data-option-number="<?= $i ?>" data-question-number="<?= $key ?>" onclick="showAnswerOptionPreview(this)" style="line-height: 3em;"><i class="show-math-button fa fa-superscript"></i></li>
        <li title="Удалить" data-option-number="<?= $i ?>" data-question-number="<?= $key ?>" onclick="deleteOption(this)" style="line-height: 3em;"><i class="deleteAnswerOption far fa-times-circle fa-2x"></i></li>
    </ul>
    <!--    --><?php //echo Html::error($model,"[$key]answerOptionsArray[{$i}]"); ?>
    <div class="option-preview-container-<?= $key ?>-<?= $i ?>  options-preview-container" style="visibility:hidden; position:absolute; top:0; left: 0">
        <div class="answer-option-preview answer-option-preview-<?= $key ?>-<?= $i ?> process-mathjax"></div>
        <div class="answer-option-buffer answer-option-buffer-<?= $key ?>-<?= $i ?> process-mathjax" style="position:absolute; top:0; left: 0"></div>
    </div>
    <script>
        var optionNumber = <?= $i ?>;
        eval("var optionPreview<?= $key ?>"+optionNumber+"=new Preview('answer-option-preview-<?= $key ?>-'+optionNumber,'answer-option-buffer-<?= $key ?>-'+optionNumber,'answer-text-area-<?= $key ?>-'+optionNumber);");
        //eval("optionPreview<?//= $key ?>//"+optionNumber+".callback=MathJax.Callback(['CreatePreview',optionPreview<?//= $key ?>//"+optionNumber+"]);");
        eval("optionPreview<?= $key ?>"+optionNumber+".callback.autoReset=true;");

        if($('.answer-option-preview-<?= $key ?>-'+optionNumber).is(':empty')) {
            eval("optionPreview<?= $key ?>"+optionNumber+".Update();");
        }
    </script>
</div>
