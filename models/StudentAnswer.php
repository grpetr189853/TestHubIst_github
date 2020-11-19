<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student_answer".
 *
 * @property int $id
 * @property int $question_id
 * @property int $student_id
 * @property int|null $answer_id
 * @property string|null $answer_text
 * @property float|null $answer_number
 * @property int $exec_time
 * @property int|null $result
 * @property int|null $test_result
 *
 * @property SManyAnswers[] $sManyAnswers
 * @property AnswerOptions[] $sAnswers
 * @property Question $question
 * @property StudentTest $testResult
 */
class StudentAnswer extends \yii\db\ActiveRecord
{

    const SCENARIO_TEXT = 'string';
    const SCENARIO_NUMBER = 'numeric';
    const SCENARIO_SELECT_MANY = 'select_many';
    const SCENARIO_SELECT_ONE = 'select_one';

    public $testId;

    public $selectedAnswers;

    public $questionNumber;

    const NUMBER_M = 11;

    const NUMBER_D = 4;

    private $requiredMessage = 'Вы не ответили на вопрос';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['answer_text','required','on' => self::SCENARIO_TEXT, 'message' => $this->requiredMessage ],
            ['answer_number', 'required', 'on' => self::SCENARIO_NUMBER, 'message' => $this->requiredMessage],
            ['answer_id', 'required', 'on' => self::SCENARIO_SELECT_ONE, 'message' => $this->requiredMessage],
            ['selectedAnswers', 'required', 'on' => self::SCENARIO_SELECT_MANY, 'message' => $this->requiredMessage],
            [['question_id', 'student_id', 'exec_time'], 'required'],
            [['question_id', 'student_id', 'answer_id', 'exec_time', 'result', 'test_result'], 'integer'],
            ['answer_number', 'integer', 'integerOnly' => false, 'message' => 'Ответ должен быть в виде цифры'],
            ['answer_text','string', 'max' => 255, 'message' => 'Максимальное количество символов: 255'],
            ['answer_number', 'integer', 'numberPattern' => '/^-?\d{1,' . self::NUMBER_M . '}\.?\d{0,' . self::NUMBER_D . '}$/', 'message' => 'Число должно быть либо целым, либо с плавающей точкой. Максимум знаков относительно запятой: ' . self::NUMBER_M . ' — перед запятой, ' . self::NUMBER_D . ' — после.'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['test_result'], 'exist', 'skipOnError' => true, 'targetClass' => StudentTest::className(), 'targetAttribute' => ['test_result' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SELECT_ONE] = ['test_id','question_id','answer_id','result','test_result'];
        $scenarios[self::SCENARIO_SELECT_MANY] = ['test_id','question_id','result','selectedAnswers','test_result'];
        $scenarios[self::SCENARIO_TEXT] = ['test_id','question_id','result','answer_text','test_result'];
        $scenarios[self::SCENARIO_NUMBER] = ['test_id','question_id','result','answer_number','test_result'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'student_id' => 'Student ID',
            'answer_id' => 'Answer ID',
            'answer_text' => 'Answer Text',
            'answer_number' => 'Answer Number',
            'exec_time' => 'Exec Time',
            'result' => 'Result',
            'test_result' => 'Test Result',
        ];
    }

    /**
     * Gets query for [[SManyAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSManyAnswers()
    {
        return $this->hasMany(SManyAnswers::className(), ['answer_id' => 'id']);
    }

    /**
     * Gets query for [[SAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSAnswers()
    {
        return $this->hasMany(AnswerOptions::className(), ['id' => 's_answer'])->viaTable('s_many_answers', ['answer_id' => 'id']);
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * Gets query for [[TestResult]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestResult()
    {
        return $this->hasOne(StudentTest::className(), ['id' => 'test_result']);
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->getSManyAnswers()->exists()) {
            foreach ($this->getSManyAnswers()->all() as $selectedOption) {
                $this->selectedAnswers[] = $selectedOption->s_answer;
            }
        }

        if ($this->answer_number) {
            $this->answer_number = floatval($this->answer_number);
        }
    }

    /**
     * Проверяет ответ число на допустимые значения
     */

    public function checkNumericAnswer()
    {
        if(!preg_match('/^(\d)+[\.,]?(\d)*$/', $this->answer_number, $matches)) {
            $this->addError('answer_number', 'Неверный формат числа');

            return false;
        }

        if(mb_strlen(strval($matches[1])) > self::NUMBER_M) {
            $this->addError('answer_number', 'Число превышает значение максимально допустимого');
            return false;
        }

        if(mb_strlen(strval($matches[2])) > self::NUMBER_D) {
            $this->addError('answer_number', 'Максимально количество знаков после запятой — четыре');
            return false;
        }
    }

    public function compareAnswer()
    {
        $this->result = 0;

        if ($this->scenario == 'select_one') {
            if ($this->answer_id == $this->question->answer_id) {
                $this->result = $this->question->difficulty;
            }
        }

        if ($this->scenario == 'select_many') {
            $correctOptionsId = array();

            foreach ($this->question->getCorrectAnswers()->all() as $optionObject) {
                $correctOptionsId[] = $optionObject->c_answer;
            }

            if (count($this->selectedAnswers) == count($correctOptionsId) && array_diff($correctOptionsId, $this->selectedAnswers) == false) {
                $this->result = $this->question->difficulty;
            }
        }

        if ($this->scenario == 'numeric') {
            $userAnswerPrecision = ($this->answer_number / 100) * $this->question->precision_percent;

            if (abs($this->question->answer_number - $this->answer_number) <= $userAnswerPrecision) {
                $this->result = $this->question->difficulty;
            }
        }

        if ($this->scenario == 'string') {
            $answer = array(
                'correct' => mb_strtolower($this->question->answer_text),
                'studentAnswer' => mb_strtolower($this->answer_text)
            );

            $formatAnswer = str_replace(' ', '', $answer);

            if ($formatAnswer['correct'] === $formatAnswer['studentAnswer']) {
                $this->result = $this->question->difficulty;
            }
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $testResult = (new StudentTest())->find()->where('test_id=:testId AND student_id=:studentId', [
                    ':testId' => $this->testId,
                    ':studentId' => Yii::$app->user->getId(),
                ])->one();

                $this->test_result = $testResult->id;
            }

            $this->compareAnswer();

            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->scenario === 'select_many') {

            if (! $this->isNewRecord) {
                SManyAnswers::deleteAll('answer_id=:answerId', array(
                    ':answerId' => $this->id
                ));
            }
            if ($this->selectedAnswers) {
                $selectedAnswersId = array();
                foreach ($this->selectedAnswers as $selectedAnswer) {
                    array_push($selectedAnswersId, array(
                        'answer_id' => $this->id,
                        's_answer' => $selectedAnswer
                    ));
                }

                $builder = Yii::$app->db->createCommand();
                $insertManyAnswer = $builder->batchInsert('s_many_answers', ['answer_id','s_answer'], $selectedAnswersId);
                $insertManyAnswer->execute();
            }
        }
    }

    /**
     * Depending on whether there is an answer to the question with ID $ questionId, we return the response model.
     * @param $questionId integer
     * @param $questionType string
     * @param $testId integer
     * @return mixed
     */
    public function getAnswerModel($questionId, $questionType, $testId)
    {
        $studentCurrentAnswer = $this->find()->where('question_id=:questionId AND student_id=:studentId', [
            ':questionId' => $questionId,
            ':studentId' => Yii::$app->user->getId(),
        ])->one();

        if ($studentCurrentAnswer) {
            $answerModel = $studentCurrentAnswer;
            $answerModel->scenario = $questionType;
        } else {
            $answerModel = new StudentAnswer();
            $answerModel->scenario = $questionType;
            $answerModel->testId = $testId;
            $answerModel->student_id = Yii::$app->user->getId();
        }

        return $answerModel;
    }
}
