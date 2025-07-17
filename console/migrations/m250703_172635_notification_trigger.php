<?php

use yii\db\Migration;

class m250703_172635_notification_trigger extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%notification_trigger}}', [
            'id' => $this->primaryKey(),
            'route' => $this->string()->notNull()->unique(), // e.g., 'document/create'
            'notification_key' => $this->string()->notNull(), // must match existing Notification.key
            'model_class' => $this->string()->notNull(), // e.g., common\models\Document
            'model_id_param' => $this->string()->notNull(), // e.g., 'id'
            'fields' => $this->json()->null(), // e.g., {"document_name": "title", "created_by": "createdBy.username"}
            'link_template' => $this->string()->null(), // e.g., '/document/view?id={id}'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%notification_trigger}}');
    }
}
