<?php

namespace app\models;

use Yii;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "question".
 *
 * @property int $id
 * @property string $title
 * @property string $type
 * @property int $difficulty
 * @property int $answer_id
 * @property string $answer_text
 * @property float|null $answer_number
 * @property float|null $precision_percent
 * @property string|null $picture
 * @property int $test_id
 *
 * @property AnswerOptions[] $answerOptions
 * @property CorrectAnswers[] $correctAnswers
 * @property AnswerOptions[] $cAnswers
 * @property Test $test
 * @property StudentAnswer[] $studentAnswers
 */
class Question extends \yii\db\ActiveRecord
{
    public $updateType;

    public $modelScenario;

    public $answerOptionsArray;

    public $answerIdTextPair;

    public $questionNumber;

    /**
     * В массиве $optionsNumber содержатся уникальные номера для css класса
     * "answer-option-#".
     * С добавлением и удалением вариантов ответа массив будет изменяться
     * Колличество элементов массива соответствует
     * количеству вариантов ответа (по умолчанию 2).
     */
    public $optionsNumber = array(
        1,
        2
    );

    public $correctAnswers;

    const TYPE_ONE = 'select_one';

    const TYPE_MANY = 'select_many';

    const TYPE_STRING = 'string';

    const TYPE_NUMERIC = 'numeric';

    const DEFAULT_PRECISION = '0.00001';

    const NUMBER_M = 11;

    const NUMBER_D = 4;

    const PRECISION_M = 6;

    const PRECISION_D = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type', 'difficulty', 'test_id'], 'required'],
            [['type'], 'string'],
            [['difficulty', 'answer_id', 'test_id'], 'integer'],
            [['answer_number', 'precision_percent'], 'number'],
            [['title', 'answer_text', 'picture'], 'string', 'max' => 65535],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
            /*
            [['correctAnswers'], 'required', 'on' => 'select'],
            [['correctAnswers'], 'safe', 'on' => 'select'],
            */
            ['correctAnswers','formatCorrectAnswers','on' => 'select','skipOnEmpty'=>false],
            ['answer_text', 'required', 'message' => 'Поле не должно быть пустым.', 'on' => 'string'],
            [['updateType', 'modelScenario'], 'safe', 'on' => 'string'],
            [['updateType', 'modelScenario'], 'safe', 'on' => 'numeric'],
            ['answer_number', 'required', 'message' => 'Поле не должно быть пустым.', 'on' => 'numeric'],
            ['answer_number', 'number', 'numberPattern' => '/^-?\d{1,' . self::NUMBER_M . '}\.?\d{0,' . self::NUMBER_D . '}$/', 'message' => 'Число должно быть либо целым, либо с плавающей точкой. Максимум знаков относительно запятой: ' . self::NUMBER_M . ' — перед запятой, ' . self::NUMBER_D . ' — после.'],
            ['precision_percent', 'number', 'numberPattern' => '/^\d{1,' . self::PRECISION_M . '}\.?\d{0,' . self::PRECISION_D . '}$/', 'message' => 'Непредусмотренная процентная точность. Максимум знаков относительно запятой: ' . self::PRECISION_M . ' — перед запятой, ' . self::PRECISION_D . ' — после.'],
            ['answerOptionsArray', 'validateAnswerOptions', 'on' => 'select','skipOnEmpty'=>false],
            ['correctAnswers', 'match', 'pattern' => '/^[\d,\s]+$/', 'message' => 'Необходимо указать номера правильных ответов из вышепреведенных вариантов. Если ответов несколько, разделите их запятой: 1, 3.', 'on' => 'select'],
            ['precision_percent', 'default', 'value' => self::DEFAULT_PRECISION, 'on' => 'numeric'],
            ['difficulty', 'number', 'integerOnly' => true, 'message' => 'Баллы должны быть в виде числа.'],
            ['type', 'in', 'range' => array(self::TYPE_ONE, self::TYPE_MANY, self::TYPE_STRING, self::TYPE_NUMERIC), 'message' => 'Указаный тип ответа не поддерживается.'],
        ];
    }

    //TODO remove the scenarios
    public function scenarios()
    {
        $scenarios = parent::scenarios();
//        return [
//            'select' => ['title', 'difficulty', 'updateType', 'modelScenario', 'correctAnswers', 'answerOptionsArray'],
//        ];
        $scenarios['select'] = ['title', 'difficulty', 'updateType', 'modelScenario', 'correctAnswers', 'answerOptionsArray','answer_id'];
        $scenarios['string'] = ['title', 'difficulty', 'updateType', 'modelScenario','answer_text'];
        $scenarios['numeric'] = ['title', 'difficulty', 'updateType', 'modelScenario','answer_number'];
        return $scenarios;
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
//            ['correctAnswers', 'answerOptionsArray']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'type' => 'Type',
            'difficulty' => 'Difficulty',
            'answer_id' => 'Answer ID',
            'answer_text' => 'Answer Text',
            'answer_number' => 'Answer Number',
            'precision_percent' => 'Precision Percent',
            'picture' => 'Picture',
            'test_id' => 'Test ID',
        ];
    }

    /**
     * Gets query for [[AnswerOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerOptions()
    {
        return $this->hasMany(AnswerOptions::className(), ['question_id' => 'id']);
    }

    /**
     * Gets query for [[CorrectAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrectAnswers()
    {
        return $this->hasMany(CorrectAnswers::className(), ['question_id' => 'id']);
    }

    /**
     * Gets query for [[CAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCAnswers()
    {
        return $this->hasMany(AnswerOptions::className(), ['id' => 'c_answer'])->viaTable('correct_answers', ['question_id' => 'id']);
    }

    /**
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }

    /**
     * Gets query for [[StudentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentAnswers()
    {
        return $this->hasMany(StudentAnswer::className(), ['question_id' => 'id']);
    }

    /**
     * Метод выполняет валидацию каждого отдельного
     */
    public function validateAnswerOptions()
    {
        if (is_array($this->answerOptionsArray)) {
            foreach ($this->answerOptionsArray as $optionNumber => $optionText) {
                if ($optionText === '') {
                    $this->addError("answerOptionsArray[{$optionNumber}]", 'Поле не должно быть пустым');
                }
            }
        } else {
            if ($this->answerOptionsArray === '') {
                $this->addError("answerOptionsArray", 'Поле не должно быть пустым');
            }
        }
    }

    public function formatCorrectAnswers()
    {
        if (! preg_match_all('/\d+/', $this->correctAnswers, $correctAnswersArray)) {
            $this->addError('correctAnswers', 'Необходимо указать номера правильных ответов из вышепреведенных вариантов. Если ответов несколько, разделите их запятой: 1, 3');
        }

        if (is_array($this->answerOptionsArray)) {
            foreach ($correctAnswersArray[0] as $correctNumber) {
                if ($correctNumber == 0 || $correctNumber > count($this->answerOptionsArray)) {
                    $this->addError('correctAnswers', 'Вариант с номером "' . $correctNumber . '" не найден.');
                }
            }
        }

        $this->correctAnswers = implode(', ', $correctAnswersArray[0]);
    }
    /*
     * protected function beforeValidate() { if ($this->isNewRecord) { switch ($this->scenario) { case 'selectOne': $this->type = self::TYPE_ONE; break; case 'selectMany': $this->type = self::TYPE_MANY; break; case 'string': $this->type = self::TYPE_STRING; break; case 'numeric': $this->type = self::TYPE_NUMERIC; break; default: $type = ''; break; } } if($this->scenario === 'numeric' && isset($this->precision_percent) == false) { $this->precision_percent = self::DEFAULT_PRECISION; } return parent::beforeValidate(); }
     */
    public function afterFind()
    {
        parent::afterFind();

        switch ($this->type) {
            case self::TYPE_ONE:
                $this->scenario = 'select';
                break;
            case self::TYPE_MANY:
                $this->scenario = 'select';
                break;
            case self::TYPE_STRING:
                $this->scenario = 'string';
                break;
            case self::TYPE_NUMERIC:
                $this->scenario = 'numeric';
                break;
            default:
                $this->scenario = '';
                break;
        }

        $this->optionsNumber = array();

        foreach ($this->answerOptions as $answerOption) {
            $this->answerOptionsArray[$answerOption->option_number] = $answerOption->option_text;
            $this->optionsNumber[] = $answerOption->option_number;
            $this->answerIdTextPair[$answerOption->id] = $answerOption->option_text;

            if ($this->type === 'select_one' && $answerOption->id == $this->answer_id) {
                $this->correctAnswers = $answerOption->option_number;
            }
        }

        if ($this->type === 'select_many') {
            $correctAnswersArray = array();

            foreach ($this->getAnswerOptions()->all() as $correctAnswer) {
                $correctAnswersArray[] = $correctAnswer->option_number;
            }

            $this->correctAnswers = implode(', ', $correctAnswersArray);
        }

        if ($this->type === 'numeric') {
            $this->answer_number = floatval($this->answer_number);
            $this->precision_percent = sprintf('%.'.self::PRECISION_D.'f', floatval($this->precision_percent));
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->correctAnswers = explode(', ', $this->correctAnswers);

            switch ($this->scenario) {
                case 'string':
                    $this->type = self::TYPE_STRING;
                    break;
                case 'numeric':
                    $this->type = self::TYPE_NUMERIC;
                    break;
                default:
                    $type = '';
                    break;
            }

            if ($this->scenario === 'select') {
                if (count($this->correctAnswers) > 1) {
                    $this->type = self::TYPE_MANY;
                } else {
                    $this->type = self::TYPE_ONE;
                }
            }

            $this->title = HtmlPurifier::process($this->title);

            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->scenario === 'select') {
            if (! $this->isNewRecord) {
                AnswerOptions::deleteAll(['question_id' => $this->id]);
            }

            $optionsArray = array();

            foreach ($this->answerOptionsArray as $number => $optionText) {
                array_push($optionsArray, array(
                    'question_id' => $this->id,
                    'option_text' => $optionText,
                    'option_number' => $number
                ));
            }

            Yii::$app->db->createCommand()->batchInsert('answer_options', ['question_id', 'option_text','option_number'],
                $optionsArray
            )->execute();

            $correctAnswers = AnswerOptions::find()->andWhere(['option_number' => $this->correctAnswers])->andWhere(['question_id' => $this->id])->all();
            if (count($correctAnswers) > 1) {
                $manyAnswersId = array();
                foreach ($correctAnswers as $correctAnswer) {
                    array_push($manyAnswersId, array(
                        'question_id' => $this->id,
                        'c_answer' => $correctAnswer->id
                    ));
                }
                Yii::$app->db->createCommand()->batchInsert('correct_answers', ['question_id','c_answer'],$manyAnswersId)->execute();
            } else {
                $singleAnswerId = $correctAnswers[0]->id;
                $this::updateAll(['answer_id' => $singleAnswerId],['id' => $this->id]);
            }

        }

//        $questionImages = new QuestionImage('saveRecord');
//        $questionImages->saveTestImages($this, 'title', $this->question_images);
    }

    public function selectCorrectNumber($answerId, $userAnswer)
    {
        /*
         * $answer = $this->find('answer_number BETWEEN :userAnswer - :userAnswer/100*precision_percent AND :userAnswer + :userAnswer/100*precision_percent AND id=:id', array( ':userAnswer' => $userAnswer, ':id' => $answerId ));
         */
        $question = $this->find()->where('id=:id', array(
            ':id' => $answerId
        ));

        $userAnswerPrecision = ($userAnswer / 100) * $question->precision_percent;

        if (abs($question->answer_number - $userAnswer) <= $userAnswerPrecision) {
            return 'Correct';
        } else {
            return 'Incorrect';
        }
    }

    public function compareTextAnswer($answerId, $userAnswer)
    {
        $question = $this->find()->where('id=:id', array(
            ':id' => $answerId
        ));

        $answer = array(
            'correct' => mb_strtolower($question->answer_text),
            'userAnswer' => mb_strtolower($userAnswer)
        );

        $formatAnswer = str_replace(' ', '', $answer);

        if ($formatAnswer['correct'] === $formatAnswer['userAnswer']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getAnswerOptionsArray()
    {
        return $this->answerOptionsArray;
    }

    /**
     * @param mixed $answerOptionsArray
     */
    public function setAnswerOptionsArray($answerOptionsArray)
    {
        $this->answerOptionsArray = $answerOptionsArray;
    }

}
