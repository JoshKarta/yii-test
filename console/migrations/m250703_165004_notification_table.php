<?php

use yii\db\Migration;

class m250703_165004_notification_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull()->unique(), // used for trigger mapping
            'title' => $this->string()->notNull(),
            'message_template' => $this->text()->notNull(), // e.g. "User {username} created a report."
            'enabled' => $this->boolean()->defaultValue(true),
            'send_email' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%notification}}');
    }
}
