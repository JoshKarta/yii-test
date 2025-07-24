<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%booking}}`.
 */
class m250723_125540_create_booking_table extends Migration
{
    public function safeUp()
    {

        $table = '{{%booking}}';
        if ($this->db->schema->getTableSchema($table, true) !== null) {
            $this->dropTable($table);
        }

        $this->createTable($table, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'start_time' => $this->dateTime()->notNull(),
            'end_time' => $this->dateTime()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('booking');
    }
}
