<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "s_many_answers".
 *
 * @property int $answer_id
 * @property int $s_answer
 *
 * @property AnswerOptions $sAnswer
 * @property StudentAnswer $answer
 */
class SManyAnswers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 's_many_answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['answer_id', 's_answer'], 'required'],
            [['answer_id', 's_answer'], 'integer'],
            [['answer_id', 's_answer'], 'unique', 'targetAttribute' => ['answer_id', 's_answer']],
            [['s_answer'], 'exist', 'skipOnError' => true, 'targetClass' => AnswerOptions::className(), 'targetAttribute' => ['s_answer' => 'id']],
            [['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentAnswer::className(), 'targetAttribute' => ['answer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'answer_id' => 'Answer ID',
            's_answer' => 'S Answer',
        ];
    }

    /**
     * Gets query for [[SAnswer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSAnswer()
    {
        return $this->hasOne(AnswerOptions::className(), ['id' => 's_answer']);
    }

    /**
     * Gets query for [[Answer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(StudentAnswer::className(), ['id' => 'answer_id']);
    }
}
