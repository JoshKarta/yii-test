<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%form}}`.
 */
class m250729_162749_create_form_table extends Migration
{
    public function up()
    {
        $this->dropTableIfExists('{{%form}}');

        $this->createTable('{{%form}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'json' => $this->json()->notNull(), // JSON definition of the form
            'created_by' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_form_created_by', '{{%form}}', 'created_by', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_form_created_by', '{{%form}}');
        $this->dropTable('{{%form}}');
    }

    protected function dropTableIfExists($table)
    {
        if (Yii::$app->db->schema->getTableSchema($table, true) !== null) {
            $this->dropTable($table);
        }
    }
}
