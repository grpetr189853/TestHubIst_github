<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "test".
 *
 * @property int $id
 * @property string $name
 * @property string $foreword
 * @property int $category_id
 * @property int $minimum_score
 * @property int $time_limit
 * @property int $attempts
 * @property string|null $create_time
 * @property string|null $deadline
 * @property int $teacher_id
 *
 * @property Question[] $questions
 * @property StudentTest[] $studentTests
 */
class Test extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'foreword', 'category_id', 'minimum_score', 'time_limit', 'attempts', 'teacher_id'], 'required'],
            [['category_id', 'minimum_score', 'time_limit', 'attempts', 'teacher_id'], 'integer'],
            [['create_time', 'deadline'], 'safe'],
            [['name', 'foreword'], 'string', 'max' => 255],
        ];
    }

    //TODO remove the scenarios
    public function scenarios()
    {
        return [
            'insert' => ['name', 'foreword', 'category_id', 'question_count', 'minimum_score', 'time_limit', 'attempts','description','deadline'],
            'update' => ['name', 'foreword', 'category_id', 'question_count', 'minimum_score', 'time_limit', 'attempts','description','deadline'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'foreword' => 'Foreword',
            'category_id' => 'Category ID',
            'minimum_score' => 'Minimum Score',
            'time_limit' => 'Time Limit',
            'attempts' => 'Attempts',
            'create_time' => 'Create Time',
            'deadline' => 'Deadline',
            'teacher_id' => 'Teacher ID',
        ];
    }

    /**
     * Gets query for [[Questions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['test_id' => 'id']);
    }

    /**
     * Gets query for [[StudentTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentTests()
    {
        return $this->hasMany(StudentTest::className(), ['test_id' => 'id']);
    }
}
