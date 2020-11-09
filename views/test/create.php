<script>
  // Класс div, который содержит в себе блоки превью и буфера, для отображения
  // результат работы MathJax.
  
  var forewordPreviewContainer = 'foreword-preview-container';

 // Переменная необходимая для переключения между редактором и MathJax превью.
 // Используется в плагине "viewPreview" редактора.
  
  var forewordRedactorDetach;
</script>
<?php

use yii\helpers\Url;

if ($test->scenario === 'insert') {
    $pageLabel = 'Создать тест';
}

if ($test->scenario === 'update') {
    $pageLabel = 'Изменить тест';
}

//$csrfTokenName = Yii::$app->request->csrfTokenName;
$csrfTokenName = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;
$validateDataUrl = \Yii::$app->urlManager->createUrl('question/validate-data');
$newOptionUrl = \Yii::$app->urlManager->createUrl('question/optionField');
/*
$cs = Yii::app()->clientScript;

$cs->registerScriptFile('https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
$cs->registerCssFile(Yii::app()->request->baseUrl.'/js/imperavi-redactor/redactor.css');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/imperavi-redactor/plugins/viewTextarea/viewTextarea.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/imperavi-redactor/redactor.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/imperavi-redactor/lang/ru.js', CClientScript::POS_HEAD);
*/
/*
$this->registerJs("
    $('.foreword-redactor').redactor({
        lang: 'ru',
        buttonsHide: ['link'],
        toolbarFixed: false,
        pastePlainText: true,
        imageLink: false,
        imageUpload: '".Url::to('testForewordImages/tmpUpload')."',
        imageUploadParam: 'TestForewordImage[imageFile]',
        pasteCallback: function(html) {
            return html.replace(/<p>(.*?)<\/p>/gi, '$1');
        },
        initCallback: function(){
            $('.test-fields').find('div[class=redactor-editor]').addClass('redactor-editor-foreword');
        },
        imageUploadErrorCallback: function(json){
            alert(json.message);
        },
        imageDeleteCallback: function(url, image){
            $.ajax({
                url:'" . Url::to('testForewordImages/deleteTmpImage') . "',
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
*/
$this->registerJs("
        $('#test-form').submit(function(event){
            if($('.question-forms').is(':empty')) {
                event.preventDefault();
                $('.qeustions-empty-error').html('Добавьте вопросы, которые будет содержать тест.');
                $('.qeustions-empty-error').addClass('alert alert-danger');
            }
        });
        
        $(document).on('blur','.questionField',function(e){
            e.preventDefault();
            var id= $(this).attr('id');
            var name= $(this).attr('name');
            var name = name.match(/\[([a-zA-Z_-]+)\]/);
            var name = name[1];
    
            $.ajax({
                url:'" . $validateDataUrl . "',
                type: 'POST',
                data: {
                    value: $.trim($(this).val()), 
                    name: name,
                    scenario: $(this).parents('div[id^=question-]').find('.js-question-type').val(),
                    //" . $csrfTokenName . ": '" . $csrfToken . "'
                },
                success :function(data){
                    var parent = $('#'+id).parent();

                    data = data.replace('<div>','<div class=\"errorMessage\">');
                    if(!$($.trim(data)).is(':empty'))
                    {
                        parent.removeClass('success');
                        parent.addClass('error');
                        if(parent.children('.errorMessage')[0]) {
                            parent.children('.errorMessage').remove();
                        }
                        parent.append(data);
                    }
                    else
                    {
                        if(!parent.hasClass('success'))
                        {                       
                            parent.removeClass('error');
                            parent.children().removeClass('error');
                            parent.addClass('success');
                            parent.children('.errorMessage').remove();
                        }
                    }

                }
           });
        });
", \yii\web\View::POS_END);

$this->registerJs("
            // Функция добавляет поле для ввода варианта ответа
    
            function addOption(input){
                var parentClass = $(input).attr('data-add');
    
                var key = parentClass.match(/\d+$/);
    
                // <div>, который содержит все варианты ответов
                var parent = $('.'+parentClass);
    
                // Вычисляем номер следующего варианта ответа
                var answerOptionNumber = parent.children('.row').size() + 1;
    
                var optionIdArray = new Array();
                var newOptionId;
    
                $('.'+parentClass+' div[class^=row]').each(function () {
                     var optionId = $(this).attr('class').match(/answer-option-(\d+)/);
                     optionIdArray.push( optionId[1] );
                });
    
                if(optionIdArray.length === 0) {
                   newOptionId = 1;
                } else {
                   newOptionId = Math.max.apply(Math, optionIdArray) + 1;
                }
    
                // GET запрос на экшен, который рендерит текстовое поле для ввода варианта
                $.ajax({
                    url:'" . $newOptionUrl . "',
                    data:{
                        i:newOptionId,
                        key:key[0],
                        number:answerOptionNumber
                    },
                    cache: false,
                    dataType: 'html',
                    success:function(data){
                        parent.append(data);
                    },
                });
            };
    
            // Функция удаляет поле варианта ответа и изменяет номера вариантов в соответствии с их количеством
    
            function deleteOption(input){
    
                // <div>, который содержит все варианты ответов
                //var optionsContainer = $(input).parents().eq(1);
                var optionsContainer = $('.js-options-'.concat($(input).attr('data-question-number')));
                
                // Удаляем указанный <div>
                $(input).parents('.answer-option-'.concat($(input).attr('data-option-number'))).remove();
                //$(input).parent().remove();
    
                // Вычисляем класс optionsContainer
                var optionsContainerClass = optionsContainer.attr('class');
    
                var optionNumber = new Array();
    
                // Добавляем в массив классы оставшихся после удаления вариантов
                $('.'+optionsContainerClass+' div[class^=answer-option-number]').each(function () {
                     optionNumber.push( $(this).attr('class') );
                });
    
                //console.log(optionNumber);
    
                var i=1;
    
                // Пересчитываем номера оставшихся в результате выполнения функции вариантов ответа
                $.each(optionNumber, function( index, value ) {
                    optionsContainer.find('.'+value).html(i+')');
                    i++;
                });
            };
", \yii\web\View::POS_HEAD);

echo "<h2 class='first-header'>{$pageLabel}</h2>";
?>
<?php


$this->registerJs("    
function Preview(classPreview, classBuffer, classTextField) {
	this.preview = $('.'+classPreview)[0];
    this.buffer = $('.'+classBuffer)[0];
    this.classTextField = classTextField;
    this.delay = 150;        // задержка перед обновлением и после нажатия клавиши
    this.timeout = null;     // setTimout id
    this.mjRunning = false;  // true есои MathJax обрабатывает полученные данные
    this.oldText = null;

    //  Переключение между buffer div - preview div и выбор нужного в заисимости от наличия 
    //  изменений в редакторе.

    this.SwapBuffers = function () {
        var buffer = this.preview, preview = this.buffer;
        this.buffer = buffer; this.preview = preview;
        buffer.style.visibility = \"hidden\"; buffer.style.position = \"absolute\";
        preview.style.position = \"\"; preview.style.visibility = \"\";
    };

    //  Метод будет вызван при нажатии клавиши в редакторе.
    //  Апдейт будет произведен после небольшого дилэя между нажатиями клавишь,
    //  что позволяет избежать ненужных обращений к MathJax при последовательном наборе.

    this.Update = function () {
        if (this.timeout) {clearTimeout(this.timeout)}
        this.timeout = setTimeout(this.callback,this.delay);
    };
    
    //
    //  Создает превью и запускает MathJax для его обработки.
    //  Если MathJax уже пытается отрендерить код, return.
    //  Если текст в редакторе не изменился, return.
    //  После обработки текста MathJax-ом вызываем PreviewDone.
    
    this.CreatePreview = function () {
    	this.timeout = null;
        if (this.mjRunning) return;
        var textElement = $('.' + this.classTextField);
        if(textElement.prop('tagName') === 'INPUT') {
        	var text = textElement.val();
        } else {
        	var text = textElement.html();
        }
        
        if (text === this.oldtext) return;
        this.buffer.innerHTML = this.oldtext = text;
        this.mjRunning = true;
        MathJax.Hub.Queue(
          [\"Typeset\",MathJax.Hub,this.buffer],
          [\"PreviewDone\",this]
        );
    };
    
    this.PreviewDone = function () {
        this.mjRunning = false;
        this.SwapBuffers();
    }
}

function showAnswerOptionPreview(element) {
    var questionNumber = $(element).attr('data-question-number');

    var optionNUmber = $(element).attr('data-option-number');

    var optionFieldWidth = $('.answer-text-area-'.concat(questionNumber, '-', optionNUmber)).css('width');
    var optionFieldHeight = $('.answer-text-area-'.concat(questionNumber, '-', optionNUmber)).css('height');
	
	var hideContainerStyles = {
		\"visibility\": \"hidden\",
		\"position\": \"absolute\"
	};
		
	var showContainerStyles = {
		\"visibility\": \"\",
		\"position\": \"\",
	};

	var optionContainer = $('.option-preview-container-'.concat(questionNumber, '-', optionNUmber));

	if(optionContainer.css('visibility') === 'hidden') {
		optionContainer.css(showContainerStyles);
	} else {
		optionContainer.css(hideContainerStyles);
	}	
}

$(function() {
    $('.test-questions-anchors').click(function() {
        if($('.questions-list-wrapper').css('display') === \"none\") {
        	$('.test-question-list > i').attr('class', 'fa fa-arrow-up');
        	$('.questions-list-header').html('Скрыть список вопросов');
        } else {
        	$('.test-question-list > i').attr('class', 'fa fa-arrow-down');
        	$('.questions-list-header').html('Показать список вопросов');
        }

        $('.questions-list-wrapper').toggle('slow');
        
    });
 });

", \yii\web\View::POS_END);
?>
<div class="test-questions-anchors">
  <div class="test-question-list">
    <i class="fa fa-arrow-down"></i>
    <span class="questions-list-header">Показать список вопросов</span>
  </div>
  <div class="questions-list-wrapper" style="display: none;">
  <?php foreach($questions as $key=>$question):?>
    <a class="question-anchor question-anchor-<?= $key+1 ?>" type="button" href="#Question<?= $key+1 ?>"><?= $key+1 ?></a>
  <?php endforeach;?>
  </div>
</div>

<?php echo $this->context->renderPartial('test_form', array('test'=>$test, 'questions'=>$questions, 'pageLabel'=>$pageLabel)); ?>