<?php

namespace app\controllers;

use app\models\Question;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\Test;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

class TestController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'getRowForm' => [
                'class' => 'app\components\DynamicTabularForm\actions\GetRowForm',
                'view' => 'question_form',
                'modelClass' => 'app\models\Question',
            ],
        ];
    }

    /*Create Test and its Questions*/
    public function actionCreate() {
        $test = new Test();
        $questions = [];
        $test->scenario = 'insert';//TODO remove this line

        /**
         * Ajax валидация будет будет доступна только для модели Test, за валидацию полей формы
         * вопросов будет отвечать экшн question/validateData
         */
//        $this->performAjaxValidation($test);

        if (isset($_POST['Test'])) {
            $test->attributes = $_POST['Test'];

            if (isset($_POST['Question'])) {
                $questions = array();

                /**
                 * Каждый элемент массива $_POST['Question'] является набором атрибутов
                 * для каждого отдельного вопроса.
                 * Соответственно мы создаем для каждого элемента
                 * новый экземпляр модели "Question" и помещаем его в массив $questions.
                 */

                foreach ($_POST['Question'] as $key => $value) {
                    $question = new Question();
                    $question->attributes = $value;
                    $question->scenario = $value['modelScenario'];

                    /**
                     * Если мы преподаватель создает вопрос в котором необходимо выбрать правильный ответ
                     * из ряда предложенных, срабатывает сценарий "select".
                     * Каждый вариант ответа будет
                     * расположен в массиве "answerOptionsArray".
                     */

                    if ($question->scenario === 'select') {
                        $question->answerOptionsArray = $value['answerOptionsArray'];
                        $question->correctAnswers = $value['correctAnswers'];
                        $answerOptionsId = array();
                        foreach ($value['answerOptionsArray'] as $id => $option) {
                            $answerOptionsId[] = $id;
                        }

                        /**
                         * Переменная $question->optionsNumber применяется для того, чтобы при ошибке
                         * валидции, css класс динамически добавляемого варианта ответа "answer-option-#"
                         * принимал правильное значение.
                         */

                        $question->optionsNumber = $answerOptionsId;
                    }

                    $questions[] = $question;
                }
            }

            /**
             * Ниже выполняется валидация и сохранение обеих моделей
             */

            $valid = $test->validate();
            foreach ($questions as $question) {
                $valid = $question->validate() && $valid;
            }

//            $valid = Model::validateMultiple($questions) && $valid;

            if ($valid && count($questions)) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $test->save();
                    $test->refresh();

                    foreach ($questions as $question) {
                        $question->test_id = $test->id;
                        $question->save();
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                }

                return $this->redirect(array(
                    'test/view',
                    'id' => $test->id
                ));
            }
        }

//        $test->validate();
//        foreach ($questions as $question) {
//            $question->validate();
//        }
//        Model::validateMultiple($questions);
        return  $this->render('create', [
            'test' => $test,
            'questions' => $questions
        ]);
    }


    protected function findModel($id)
    {
        if (($model = Test::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }
}