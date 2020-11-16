<?php

namespace app\controllers;

use app\components\DynamicTabularForm\DynamicTabularForm;
use app\models\Question;
use app\models\TestSearch;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;
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
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => '/files' ,
                'path' => '@app/files',
            ],
            'file-delete' => [
                'class' => 'vova07\imperavi\actions\DeleteFileAction',
                'url' => '/files',
                'path' => '@app/files',
            ],
        ];
    }

    /**
     * Create Test and its Questions
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate() {
        $test = new Test();
        $questions = [];
        $test->scenario = 'insert';//TODO remove this line

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

        return  $this->render('create', [
            'test' => $test,
            'questions' => $questions
        ]);
    }

    /**
     * Update Test and its Questions
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdate($id)
    {

        $test = $this->findModel($id);

        $questions = $test->getQuestions()->all();

        $test->scenario = 'update';//TODO remove this line

        if (isset($_POST['Test'])) {
            $test->attributes = $_POST['Test'];

            if (isset($_POST['Question'])) {
                $questions = array();
                foreach ($_POST['Question'] as $key => $value) {

                    if ($value['updateType'] == DynamicTabularForm::UPDATE_TYPE_CREATE) {
                        $question = new Question();
                    } else {
                        if ($value['updateType'] == DynamicTabularForm::UPDATE_TYPE_UPDATE) {
//                            $question = Question::model()->findByPk($value['id']);
                            $question = Question::findOne($value['id']);
                        } else {
                            if ($value['updateType'] == DynamicTabularForm::UPDATE_TYPE_DELETE) {
//                                $delete = Question::model()->findByPk($value['id']);
                                $delete = Question::findOne($value['id']);
                                if ($delete->delete()) {
                                    unset($question);
                                    continue;
                                }
                            }
                        }
                    }

                    $question->attributes = $value;
                    $question->scenario = $value['modelScenario'];

                    if ($question->scenario === 'select') {
                        $numberOfOptions = range(1, count($value['answerOptionsArray']));
                        $question->answerOptionsArray = array_combine($numberOfOptions, $value['answerOptionsArray']);
                        $question->correctAnswers = $value['correctAnswers'];

                        $answerOptionsId = array();

                        foreach ($value['answerOptionsArray'] as $id => $option) {
                            $answerOptionsId[] = $id;
                        }

                        $question->optionsNumber = $answerOptionsId;
                    }

                    $questions[] = $question;
                }
            }

            $valid = $test->validate();
            foreach ($questions as $question) {
                $valid = $question->validate() & $valid;
            }

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

        return $this->render('create', array(
            'test' => $test,
            'questions' => $questions
        ));
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view',[
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new TestSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render('index', array(
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ));
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