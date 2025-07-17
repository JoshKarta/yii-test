<?php

use yii\db\Migration;

class m250703_165155_role_notification_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%notification_role}}', [
            'notification_id' => $this->integer()->notNull(),
            'role' => $this->string()->notNull(),
        ]);
        $this->addPrimaryKey('pk_notification_role', '{{%notification_role}}', ['notification_id', 'role']);
        $this->addForeignKey('fk_notification_role_notification', '{{%notification_role}}', 'notification_id', '{{%notification}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_notification_role_notification', '{{%notification_role}}');
        $this->dropTable('{{%notification_role}}');
    }
}
