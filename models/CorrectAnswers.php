<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "correct_answers".
 *
 * @property int $question_id
 * @property int $c_answer
 *
 * @property AnswerOptions $cAnswer
 * @property Question $question
 */
class CorrectAnswers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'correct_answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'c_answer'], 'required'],
            [['question_id', 'c_answer'], 'integer'],
            [['question_id', 'c_answer'], 'unique', 'targetAttribute' => ['question_id', 'c_answer']],
            [['c_answer'], 'exist', 'skipOnError' => true, 'targetClass' => AnswerOptions::className(), 'targetAttribute' => ['c_answer' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'question_id' => 'Question ID',
            'c_answer' => 'C Answer',
        ];
    }

    /**
     * Gets query for [[CAnswer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCAnswer()
    {
        return $this->hasOne(AnswerOptions::className(), ['id' => 'c_answer']);
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
}
