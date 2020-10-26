<?php

namespace app\models;

use Yii;

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
            [['title', 'type', 'difficulty', 'answer_id', 'answer_text', 'test_id'], 'required'],
            [['type'], 'string'],
            [['difficulty', 'answer_id', 'test_id'], 'integer'],
            [['answer_number', 'precision_percent'], 'number'],
            [['title', 'answer_text', 'picture'], 'string', 'max' => 255],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
        ];
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
}
