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
            [['question_id', 'student_id', 'exec_time'], 'required'],
            [['question_id', 'student_id', 'answer_id', 'exec_time', 'result', 'test_result'], 'integer'],
            [['answer_number'], 'number'],
            [['answer_text'], 'string', 'max' => 255],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['test_result'], 'exist', 'skipOnError' => true, 'targetClass' => StudentTest::className(), 'targetAttribute' => ['test_result' => 'id']],
        ];
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
}
