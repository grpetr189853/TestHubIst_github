<?php

use yii\db\Migration;

/**
 * Class m201026_081631_create_question_table_and_relations
 */
class m201026_081631_create_question_table_and_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        /*Tests Category*/
        $this->createTable('{{%tests_category}}',[
            'id'            => $this->primaryKey(),
            'name'          => $this->string()->notNull(),
        ], $tableOptions);

        /*Test*/
        $this->createTable('{{%test}}', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string()->notNull(),
            'foreword'      => $this->string()->notNull(),
            'category_id'   => $this->integer()->notNull(),
            'minimum_score' => $this->integer()->notNull(),
            'time_limit'    => $this->integer()->notNull(),
            'attempts'      => $this->integer()->notNull(),
            'create_time'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deadline'      => $this->timestamp()->defaultExpression('NULL'),
            'teacher_id'    => $this->integer()->notNull(),
        ], $tableOptions);

        /*Questions*/
        $this->createTable('{{%question}}',[
            'id'            => $this->primaryKey(),
            'title'         => $this->string()->notNull(),
            'type'          => 'ENUM("select_one", "select_many", "numeric", "string") NOT NULL',
            'difficulty'    => $this->integer()->notNull(),
            'answer_id'     => $this->integer()->notNull(),
            'answer_text'   => $this->string()->notNull(),
            'answer_number' => $this->decimal(15,4)->null()->defaultValue(null),
            'precision_percent' => $this->decimal(6,5)->null()->defaultValue(null),
            'picture'       => $this->string()->null()->defaultValue(null),
            'test_id'       => $this->integer()->notNull(),
        ], $tableOptions);

        /*Student_answer*/
        $this->createTable('{{%student_answer}}',[
            'id'            => $this->primaryKey(),
            'question_id'   => $this->integer()->notNull(),
            'student_id'    => $this->integer()->notNull(),
            'answer_id'     => $this->integer()->null(),
            'answer_text'   => $this->string(),
            'answer_number' => $this->decimal(9,4)->null(),
            'exec_time'     => $this->integer()->notNull(),
            'result'        => $this->integer()->null(),
            'test_result'   => $this->integer()->null(),
        ], $tableOptions);

        /*Student_test*/
        $this->createTable('{{%student_test}}',[
            'id'            => $this->primaryKey(),
            'attempts'      => $this->integer()->null(),
            'deadline'      => $this->timestamp()->defaultExpression('NULL'),
            'result'        => $this->integer()->null(),
            'test_id'       => $this->integer()->notNull(),
            'student_id'    => $this->integer()->notNull(),
            'start_time'    => $this->timestamp()->defaultExpression('NULL'),
            'end_time'      => $this->timestamp()->defaultExpression('NULL'),
        ], $tableOptions);

        /*Answer_options*/
        $this->createTable('{{%answer_options}}',[
            'id'            => $this->primaryKey(),
            'question_id'   => $this->integer()->notNull(),
            'option_text'   => $this->string()->notNull(),
            'option_number' => $this->integer()->notNull(),
        ], $tableOptions);

        /*correct_answers*/
        $this->createTable('{{%correct_answers}}',[
            'question_id'   => $this->integer()->notNull(),
            'c_answer'      => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('correct_answers_pk', '{{%correct_answers}}', ['question_id', 'c_answer']);

        /*s_many_answers*/
        $this->createTable('{{%s_many_answers}}',[
            'answer_id'     => $this->integer()->notNull(),
            's_answer'      => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('s_many_answers_pk','{{%s_many_answers}}',['answer_id','s_answer']);

        /*Relations and Indexes*/

        $this->createIndex('index-answer_options-question_id','{{%answer_options}}','question_id');
        $this->addForeignKey('fk-answer_options-question','{{%answer_options}}','question_id','{{%question}}','id', 'CASCADE', 'RESTRICT');

        $this->createIndex('index-correct_answers-question_id','{{%correct_answers}}','question_id');
        $this->createIndex('index-correct_answers','{{%correct_answers}}','c_answer');
        $this->addForeignKey('fk-correct_answers-question_id','{{%correct_answers}}','question_id','{{%question}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-correct_answers-answer_options','{{%correct_answers}}','c_answer','{{%answer_options}}','id', 'CASCADE', 'RESTRICT');

        $this->createIndex('index-s_many_answers-answer_id','{{%s_many_answers}}','answer_id');
        $this->createIndex('index-s_many_answers-s_answer','{{%s_many_answers}}','s_answer');
        $this->addForeignKey('fk-s_many_answers-student_answer','{{%s_many_answers}}','answer_id','{{%student_answer}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-s_many_answers-answer_options','{{%s_many_answers}}','s_answer','{{%answer_options}}', 'id', 'CASCADE', 'RESTRICT');

        $this->createIndex('index-question-test_id','{{%question}}','test_id');
        $this->addForeignKey('fk-question-test','{{%question}}','test_id','{{%test}}','id', 'CASCADE', 'RESTRICT');

        $this->createIndex('index-student_answer-question_id','{{%student_answer}}','question_id');
        $this->createIndex('index-student_answer-test_result','{{%student_answer}}','test_result');
        $this->addForeignKey('fk-student_answer-question','{{%student_answer}}', 'question_id', '{{%question}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-student_answer-student_test','{{%student_answer}}', 'test_result', '{{%student_test}}', 'id', 'CASCADE', 'RESTRICT');

        $this->createIndex('index-student_test-test_id','{{%student_test}}','test_id');
        $this->addForeignKey('fk-student_test-test','{{%student_test}}','test_id','{{%test}}','id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /*Drop relations*/
        $this->dropForeignKey('fk-student_test-test','{{%student_test}}');
        $this->dropIndex('index-student_test-test_id','{{%student_test}}');

        $this->dropForeignKey('fk-student_answer-student_test','{{%student_answer}}');
        $this->dropForeignKey('fk-student_answer-question','{{%student_answer}}');
        $this->dropIndex('index-student_answer-test_result','{{%student_answer}}');
        $this->dropIndex('index-student_answer-question_id','{{%student_answer}}');

        $this->dropForeignKey('fk-question-test','{{%question}}');
        $this->dropIndex('index-question-test_id','{{%question}}');

        $this->dropForeignKey('fk-s_many_answers-answer_options','{{%s_many_answers}}');
        $this->dropForeignKey('fk-s_many_answers-student_answer','{{%s_many_answers}}');
        $this->dropIndex('index-s_many_answers-s_answer','{{%s_many_answers}}');
        $this->dropIndex('index-s_many_answers-answer_id','{{%s_many_answers}}');

        $this->dropForeignKey('fk-correct_answers-answer_options','{{%correct_answers}}');
        $this->dropForeignKey('fk-correct_answers-question_id','{{%correct_answers}}');
        $this->dropIndex('index-correct_answers','{{%correct_answers}}');
        $this->dropIndex('index-correct_answers-question_id','{{%correct_answers}}');

        $this->dropForeignKey('fk-answer_options-question','{{%answer_options}}');
        $this->dropIndex('index-answer_options-question_id','{{%answer_options}}');

        /*Drop tables*/
        $this->dropTable('{{%tests_category}}');
        $this->dropTable('{{%test}}');
        $this->dropTable('{{%question}}');
        $this->dropTable('{{%student_answer}}');
        $this->dropTable('{{student_test}}');
        $this->dropTable('{{%answer_options}}');
        $this->dropTable('{{%correct_answers}}');
        $this->dropTable('{{%s_many_answers}}');
    }
}
