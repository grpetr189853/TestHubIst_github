<?php

use app\components\DynamicTabularForm\DynamicTabularForm;
use vova07\imperavi\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $test app\models\Test */
/* @var $questions array */
/* @var $pageLabel string */
/* @var $form app\components\DynamicTabularForm\DynamicTabularForm */

$form = DynamicTabularForm::begin(array(
        'defaultRowView'=>'question_form',
        'id'=>'test-form',
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>true,
    ));

?>

<div class="form th-test-from ignore-mathjax">

    <div class="test-fields">
        <div class="row">
            <?= $form->field($test,'name')->textInput() ?>
        </div>
        <div class="row">

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
            <?= $form->field($test,'minimum_score')->textInput()?>
        </div>

        <div class="row">
            <?= $form->field($test,'time_limit')->textInput()?>
        </div>

        <div class="row">
            <?= $form->field($test,'attempts')->textInput()?>
        </div>

        <div class="row">
            <?php
                $dateTimeHtmlOptions = array();

                if(!strtotime($test->deadline) || strtotime($test->deadline) < 0) {
                    $dateTimeHtmlOptions = array('value'=>'гггг-мм-дд чч:мм');
                }

                echo $form->field($test,'deadline')->textInput();
            ?>

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
