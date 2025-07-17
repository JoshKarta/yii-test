<?php

use yii\db\Migration;

class m250703_165118_user_notification_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user_notification}}', [
            'id' => $this->primaryKey(),
            'notification_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'is_read' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk_user_notification_notification', '{{%user_notification}}', 'notification_id', '{{%notification}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_user_notification_user', '{{%user_notification}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_notification_notification', '{{%user_notification}}');
        $this->dropForeignKey('fk_user_notification_user', '{{%user_notification}}');
        $this->dropTable('{{%user_notification}}');
    }
}
