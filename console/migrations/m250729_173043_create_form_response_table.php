<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%form_response}}`.
 */
class m250729_173043_create_form_response_table extends Migration
{
    public function up()
    {
        $this->dropTableIfExists('{{%form_response}}');

        $this->createTable('{{%form_response}}', [
            'id' => $this->primaryKey(),
            'form_id' => $this->integer()->notNull(),
            'response_json' => $this->json()->notNull(), // submitted data
            'submitted_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_form_response_form',
            '{{%form_response}}',
            'form_id',
            '{{%form}}',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_form_response_form', '{{%form_response}}');
        $this->dropTable('{{%form_response}}');
    }

    protected function dropTableIfExists($table)
    {
        if (Yii::$app->db->schema->getTableSchema($table, true) !== null) {
            $this->dropTable($table);
        }
    }
}
