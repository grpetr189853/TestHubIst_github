<?php

use yii\db\Migration;

/**
 * Class m201123_102607_user_table_fixes
 */
class m201123_102607_user_table_fixes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'verification_token', $this->string()->defaultValue(null));
        $this->addColumn('{{%user}}', 'type', "ENUM('student', 'teacher', 'admin')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}','verification_token');
        $this->dropColumn('{{%user}}', 'type');
    }

}
