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
        // $this->execute("create type user_type as enum ('student', 'teacher', 'admin')");
        $this->addColumn('{{%user}}', 'verification_token', $this->string()->defaultValue(null));
        $this->addColumn('{{%user}}', 'type', "user_type");
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
