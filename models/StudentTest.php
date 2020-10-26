<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student_test".
 *
 * @property int $id
 * @property int|null $attempts
 * @property string|null $deadline
 * @property int|null $result
 * @property int $test_id
 * @property int $student_id
 * @property string|null $start_time
 * @property string|null $end_time
 *
 * @property StudentAnswer[] $studentAnswers
 * @property Test $test
 */
class StudentTest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attempts', 'result', 'test_id', 'student_id'], 'integer'],
            [['deadline', 'start_time', 'end_time'], 'safe'],
            [['test_id', 'student_id'], 'required'],
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
            'attempts' => 'Attempts',
            'deadline' => 'Deadline',
            'result' => 'Result',
            'test_id' => 'Test ID',
            'student_id' => 'Student ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
        ];
    }

    /**
     * Gets query for [[StudentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentAnswers()
    {
        return $this->hasMany(StudentAnswer::className(), ['test_result' => 'id']);
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
}
