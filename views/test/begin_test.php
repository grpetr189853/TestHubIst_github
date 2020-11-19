<?php

use app\assets\JQCAsset;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $test \app\models\Test */
/* @var $directQuestionNumber integer */
/* @var $answerModel \app\models\StudentAnswer */
/* @var $questionDataArray array */
/* @var $questionNumberIdPair array */
/* @var $testTimeLimit integer */
/* @var $testStartTime \DateTime*/


JQCAsset::register($this);
$studentAnswersQuestionId = $test->getStudentAnswersByQuestionsId($questionNumberIdPair); ?>

<?php
$this->registerJs('
function swapQuestion(href) {
//	var regexp = /^.+[\/\?]q[\=\/]([\d\w]+)$/i;
	var regexp = new RegExp(/^.+\/([\d|\w]+)$/i);
	var questionNumber = 1;
	if(matches = href.match(regexp)) {
		questionNumber = matches[1];
	}
//	questionNumber = '. \Yii::$app->request->get('q') .';
	if(questionNumber === \'end\') {
        $(\'.question-anchors\').css(\'display\', \'block\');
        $(\'.question-anchors\').off(\'mouseleave\');
	}
	
	var data = {};
	data[\'testID\'] = "'.  $test->id .'";
	data[\'questionDataArray\'] =  '. JSON::encode(array('questionDataArray' => $questionDataArray)) .'.questionDataArray[ questionNumber ];
	data[\'questionNumberIdPair\'] = '. JSON::encode($questionNumberIdPair) .';
	data[\'questionNumber\'] = questionNumber;
	data[\'testTimeLimit\'] = "'. $testTimeLimit .'";
	data[\'testStartTime\'] = "'. $testStartTime .'";

	$.ajax({
        url:"'. Yii::$app->urlManager->createUrl('test/post-question') .'",
        type: "POST",
        data: data,
        success: function(data){
            document.getElementById("answer-question-form").innerHTML = data;
            changeSkipQuestionButton();
        },
        error: function(xhr, status, error) {
        	var err = eval("(" + xhr.responseText + ")");
        	console.log(err.Message);
        }
    });
}


function setupHistoryClicks() {
	$("a.question-anchor").each(function() {
		addClicker(this);
    });
}

function addClicker(link) {
    link.addEventListener("click", function(e) {
	    swapQuestion(link.href);
	    history.pushState(null, null, link.href);
	    e.preventDefault();
	}, false);
}

function changeSkipQuestionButton() {
	if(nextQuestionNumber = document.getElementById(\'nextQuestionNumber\')) {
		var href = "'. Url::to(['test/process','id' => $test->id]) . '"+"/"+ nextQuestionNumber.value;
		$(\'.answer-buttons-container\').append(\'<a id="skip-question" class="btn btn-danger skip-question" type="button" href="\'+href+\'">Пропустить</a>\');
		addClicker(document.getElementById("skip-question"));
    } else {
    	$(\'.skip-question\').css(\'display\', \'none\');
    }
}

function toggleQuestionAnchors() {
	$(\'.question-anchors\').toggle(\'500\');
}

function serverTime() {
	var time = null; 
    $.ajax({url: "'. Yii::$app->urlManager->createUrl('site/get-server-time') .'", 
        async: false, dataType: \'text\', 
        success: function(text) { 
            time = new Date(text); 
        }, error: function(http, message, exc) { 
            time = new Date(); 
    }}); 
    return time; 
}

window.onload = function() {
	var timeLimit = "'. $testTimeLimit .'";
	var countdownFormat = \'MS\';
	if(timeLimit > 3600) {
		countdownFormat = \'HMS\';
	}
	
	$(\'.test-countdown-clock\').countdown({
		until: new Date("'. date("Y/m/d H:i:s", $testStartTime + $testTimeLimit) .'"),
		serverSync: serverTime,
		format: countdownFormat,
		compact: true,
		onExpiry: function() {
			document.getElementById("answer-form").submit();
		}
	});
	

	if (!supports_history_api()) { return; }
	setupHistoryClicks();
	window.setTimeout(function() {
	  window.addEventListener("popstate", function(e) {
	    swapQuestion(location.pathname);
	  }, false);
	}, 1);
	
	changeSkipQuestionButton();
	
	$(\'.answer-question-form\').on(\'click\', \'.test-question-counter\', function() {
		toggleQuestionAnchors();
    });
	$(\'.question-anchors\').mouseleave(function() {
		toggleQuestionAnchors();
	});
}

function supports_history_api() {
	  return !!(window.history && history.pushState);
}

');
?>

<div id="answer-question-form" class=" form answer-question-form">
    <?php

    echo $this->context->renderPartial('test_question', array(
        'answerModel' => $answerModel,
        'testID' => $test->id,
        'questionNumber' => $directQuestionNumber,
        'questionNumberIdPair' => $questionNumberIdPair,
        'questionDataArray' => $questionDataArray[$directQuestionNumber],
        'numberOfQuestions' => count($questionNumberIdPair),
        'studentAnswersQuestionId' => $studentAnswersQuestionId,
        'testTimeLimit' => $testTimeLimit,
        'testStartTime' => $testStartTime,
        'questionAlert' => ''
    ));
    ?>
</div>