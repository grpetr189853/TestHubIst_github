<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "answer_options".
 *
 * @property int $id
 * @property int $question_id
 * @property string $option_text
 * @property int $option_number
 *
 * @property Question $question
 * @property CorrectAnswers[] $correctAnswers
 * @property Question[] $questions
 * @property SManyAnswers[] $sManyAnswers
 * @property StudentAnswer[] $answers
 */
class AnswerOptions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'answer_options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'option_text', 'option_number'], 'required'],
            [['question_id', 'option_number'], 'integer'],
            [['option_text'], 'string', 'max' => 255],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
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
            'option_text' => 'Option Text',
            'option_number' => 'Option Number',
        ];
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
     * Gets query for [[CorrectAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrectAnswers()
    {
        return $this->hasMany(CorrectAnswers::className(), ['c_answer' => 'id']);
    }

    /**
     * Gets query for [[Questions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['id' => 'question_id'])->viaTable('correct_answers', ['c_answer' => 'id']);
    }

    /**
     * Gets query for [[SManyAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSManyAnswers()
    {
        return $this->hasMany(SManyAnswers::className(), ['s_answer' => 'id']);
    }

    /**
     * Gets query for [[Answers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(StudentAnswer::className(), ['id' => 'answer_id'])->viaTable('s_many_answers', ['s_answer' => 'id']);
    }
}
