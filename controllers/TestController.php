<?php

namespace app\controllers;

use app\components\DynamicTabularForm\DynamicTabularForm;
use app\models\Question;
use app\models\StudentAnswer;
use app\models\StudentTest;
use app\models\TestSearch;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update','init','process','result','assign-test','view'],
                'rules' => [
                    [
                        'actions' => ['create','update','assign-test','view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(\Yii::$app->user->identity->username)||User::isUserTeacher(\Yii::$app->user->identity->username);
                        }
                    ],
                    [
                        'actions' => ['init','process','result'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(\Yii::$app->user->identity->username)||User::isUserStudent(\Yii::$app->user->identity->username);
                        }
                    ],
                ],
            ],
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

    /**
     * The action is performed when the student starts the test.
     * If successful, the browser will be redirected to the first question of the test.
     * @param integer $id
     * @return mixed
     */
    public function actionInit($id)
    {
        // Init the test and reset the test results
        $studentTest = StudentTest::find()->where('test_id=:testId AND student_id=:studentId', [
            'testId' => $id,
            'studentId' => \Yii::$app->user->getId(),
        ])->one();
        $studentTest->attempts -= 1;
        $studentTest->start_time = date('Y-m-d H:i:s');
        $studentTest->end_time = null;
        try {
            $studentTest->update();
        } catch (StaleObjectException $e) {
        } catch (\Throwable $e) {
        }
        if(StudentAnswer::find()->where('student_id=:studentId',[
            'studentId'     => \Yii::$app->user->getId(),
        ])->exists()) {
            $answerModel = StudentAnswer::find()->where('student_id=:studentId',[
                'studentId'     => \Yii::$app->user->getId(),
            ])->all();
            foreach ($answerModel as $answer){
                try {
                    $answer->delete();
                } catch (StaleObjectException $e) {
                } catch (\Throwable $e) {
                }
            }
        }

        return $this->redirect(array(
            'process',
            'id' => $id
        ));

    }

    /**
     * Performs the test.
     * @param integer $id
     * @param null $q
     * @return mixed
     * @throws \yii\base\ExitException
     * @throws \yii\web\HttpException
     */
    public function actionProcess($id, $q = null) {
        $userAnswers = Yii::$app->request->post('StudentAnswer');
        if(isset($userAnswers)){
            /**
             * Before accepting an answer for verification, we check if the time has passed for the test.
             * If the time is up, we will redirect to the page with the result.
             */
            if (! (new Test)->checkTestTimeLimit(Yii::$app->request->post('testStartTime'), Yii::$app->request->post('testTimeLimit'))) {
                Yii::$app->session->set('endTest', true);
                if (Yii::$app->request->isAjax) {
                    echo Json::encode([
                        'redirect' => Yii::$app->urlManager->createUrl('test/result', [
                            'id' => Yii::$app->request->post('testId'),
                        ])
                    ]);

                    Yii::$app->end();
                } else {
                    $this->redirect(array(
                        'result',
                        'id' => Yii::$app->request->post('testId'),
                    ));
                }
            }

            /**
             * checks if the answer to the question exists - and if it does, it updates it; if it doesn't exist, it stores it in the database
             */
            $answerModel = new StudentAnswer();


            $studentAnswer = ($answerModel)->getAnswerModel(Yii::$app->request->post('StudentAnswer')['question_id'], Yii::$app->request->post('StudentAnswer')['scenario'], Yii::$app->request->post('testId'));

            $studentAnswer->attributes = Yii::$app->request->post('StudentAnswer');

            if (isset(Yii::$app->request->post('StudentAnswer')['selectedAnswers'])) {
                $studentAnswer->selectedAnswers = Yii::$app->request->post('StudentAnswer')['selectedAnswers'];
            }

            if ($studentAnswer->validate()) {
                $studentAnswer->save(false);
                echo JSON::encode(array(
                    'validateStatus' => 'success'
                ));
            } else {
                var_dump($studentAnswer->getErrors());
            }

            Yii::$app->end();
        }

        $test = Test::find()->where('id=:testId',['testId'=> $id])->one();//TODO: заменить методом loadModel()
        $test_questions = Question::find()->where('test_id=:testId',['testId'=> $id])->all();

        $directQuestionNumber = 1;

        if ($directQuestionNumber = Yii::$app->request->get('q')) {
            if ($directQuestionNumber === 'end') {
                $this->redirect([
                    'process',
                    'id' => $test->id
                ]);
            }
        } else {
            $directQuestionNumber = 1;
        }

        if ($directQuestionNumber > count($test_questions)) {
            throw new \yii\web\HttpException(404,'Not Found.');
        }

        $studentTest = StudentTest::find()->where('test_id=:testId  AND student_id=:studentId', ['testId' => $id,'studentId' => \Yii::$app->user->getId()])->one();

        $questionDataArray = array(); //An array containing the necessary information about all questionsх
        $questionNumberIdPair = array(); // Array question number => question ID

        foreach ($test_questions as $key => $question) {
            $questionDataArray[$key + 1] = array(
                'id' => $question->id,
                'title' => $question->title,
                'type' => $question->type,
                'answerIdTextPair' => $question->answerIdTextPair ,// The array contains a pair of answer option ID => Answer option textа
            );
            $questionNumberIdPair[$key + 1] = $question->id;
        }

        $answerModel = new StudentAnswer();

        return $this->render('test_starter',[
            'tests_questions'  => $test_questions,
            'test'         => $test,
            'directQuestionNumber' => $directQuestionNumber,
            'answerModel' => $answerModel,
            'questionDataArray' => $questionDataArray,
            'questionNumberIdPair' => $questionNumberIdPair,
            'testTimeLimit' => $test->time_limit * 60,
            'testStartTime' => strtotime($studentTest->start_time)
        ]);
    }

    /**
     * The action only allows POST requests and is intended to dynamically display the requested question.
     * @return mixed
     */
    public function actionPostQuestion()
    {
        if (isset($_POST) && isset($_POST['questionNumber']) && isset($_POST['questionNumberIdPair'])) {
            $questionNumber = $_POST['questionNumber'];
            $numberOfQuestions = count($_POST['questionNumberIdPair']);

            $questionDataArray = array();
            $studentAnswersQuestionId = array();
            $answerModel = null;

            if (Yii::$app->request->post('questionDataArray')) {
                $questionDataArray = $_POST['questionDataArray'];
            }

            $studentAnswersQuestionId = Test::getStudentAnswersByQuestionsId($_POST['questionNumberIdPair']);

            $questionAlert = '';

            if ($questionNumber == 'end') {
                $questionAlert = 'Завершить тест?';

                if (count($studentAnswersQuestionId) < $numberOfQuestions) {
                    $questionAlert = 'Вы ответили не на все вопросы. ' . $questionAlert;
                }
            }
            $answerModel = new StudentAnswer();
            return $this->renderPartial('test_question', array(
                'answerModel' => $answerModel,
                'testID' => $_POST['testID'],
                'questionNumber' => $questionNumber,
                'questionNumberIdPair' => $_POST['questionNumberIdPair'],
                'questionDataArray' => $questionDataArray,
                'questionAlert' => $questionAlert,
                'numberOfQuestions' => $numberOfQuestions,
                'studentAnswersQuestionId' => $studentAnswersQuestionId,
                'testTimeLimit' => $_POST['testTimeLimit'],
                'testStartTime' => $_POST['testStartTime']
            ));
        }
    }

    /**
     * If the test has not yet been run or is re-running, the action counts
     * test result and brings the record in the UsersTests table to the appropriate form
     * (assigns values to the result and end_time columns). Otherwise, just print the result of
     * test execution.
     * @param $id
     * @return mixed
     */
    public function actionResult($id)
    {
        $studentTest = StudentTest::find()->where('test_id=:testId AND student_id=:studentId', array(
            ':testId' => $id,
            ':studentId' => Yii::$app->user->identity->id
        ))->one();

        if ($studentTest) {
            $studentTotalScore = $studentTest->result;
            $timeOutMessage = '';

            if (isset($_POST['endTest']) || Yii::$app->session->get('endTest') === true) {

                $studentTotalScore = 0;
                if ($studentTest->studentAnswers) {
                    foreach ($studentTest->studentAnswers as $answer) {
                        $studentTotalScore = $studentTotalScore + $answer->result;
                    }
                }

                $studentTest->result = $studentTotalScore;
                $studentTest->end_time = date('Y-m-d H:i');

                $timeLimit = strtotime($studentTest->start_time) + $studentTest->test->time_limit * 60;

                if (strtotime($studentTest->end_time) > $timeLimit) {
                    $studentTest->end_time = date('Y-m-d H:i', $timeLimit);
                }

                try {
                    $studentTest->update();
                } catch (StaleObjectException $e) {
                    } catch (\Throwable $e) {
                }
                if (Yii::$app->session->get('endTest')) {
                    $timeOutMessage = 'Время, отведенное на выполнение теста, вышло.';
                }

                Yii::$app->session->set('endTest', null);

                return $this->redirect(array(
                    'result',
                    'id' => $id
                ));
            }

            if (is_null($studentTotalScore)) {
                return $this->redirect(array(
                    'view',
                    'id' => $id
                ));
            }

            $message = 'Тест был успешно сдан.';
            $testPassed = true;

            if ($studentTotalScore < $studentTest->test->minimum_score) {
                $message = 'Вы набрали недостаточно баллов для прохождения теста';
                $testPassed = false;
            }

            return $this->render('test_result', array(
                'totalScore' => $studentTotalScore,
                'studentTest' => $studentTest,
                'message' => $message,
                'timeOutMessage' => $timeOutMessage,
                'testPassed' => $testPassed
            ));
        } else {
            return $this->redirect(array(
                'view',
                'id' => $id
            ));
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view',[
            'model' => $model,
        ]);
    }

    public function actionAssignTest($id) {
        $searchModel = new \app\models\UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $studentTestArray = StudentTest::findAll(['test_id' => $id]);
        if(Yii::$app->request->isAjax && isset($_POST['student_id'])){
            $doDelete = ($_POST['doDelete'] == 'false') ? false: true;
            if($doDelete){
                $deletedTests = StudentTest::find()->where(['student_id'=> $_POST['student_id']])->andWhere(['test_id'=>$id])->all();
                foreach ($deletedTests as $test){
                    $test->delete();
                }
            } else {
                $model = Test::findOne(['id'=> $id]);
                $student_test = new StudentTest();
                $student_test->test_id = $model->id;
                $student_test->attempts = $model->attempts;
                $student_test->student_id = $_POST['student_id'];
                $student_test->deadline = $model->deadline;
                $student_test->save();

            }

        }
        return $this->render('list',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'studentTestArray' => $studentTestArray,
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