<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%config_category}}`.
 */
class m251120_135049_create_config_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%config_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
            'description' => $this->text()->null(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_system' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Insert default categories
        $this->batchInsert(
            '{{%config_category}}',
            ['name', 'description', 'sort_order', 'is_system'],
            [
                ['general', 'General application settings', 1, true],
                ['company', 'Company information and details', 2, false],
                ['frontend', 'Frontend application settings', 3, false],
                ['backend', 'Backend application settings', 4, false],
                ['email', 'Email server and template settings', 5, true],
                ['system', 'System-level configurations', 6, true],
            ]
        );

        // Add foreign key to config table
        $this->addColumn('{{%config}}', 'category_id', $this->integer()->after('id'));

        // Migrate existing category names to category IDs
        $categories = (new \yii\db\Query())
            ->select(['id', 'name'])
            ->from('{{%config_category}}')
            ->all();

        foreach ($categories as $category) {
            $this->update(
                '{{%config}}',
                ['category_id' => $category['id']],
                ['category' => $category['name']]
            );
        }

        $this->dropColumn('{{%config}}', 'category');
        $this->createIndex('idx-config-category_id', '{{%config}}', 'category_id');
        $this->addForeignKey(
            'fk-config-category_id',
            '{{%config}}',
            'category_id',
            '{{%config_category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%config}}', 'category', $this->string(50)->after('id'));

        // Migrate back category IDs to names
        $configs = (new \yii\db\Query())
            ->select(['c.id', 'cat.name'])
            ->from(['c' => '{{%config}}'])
            ->leftJoin(['cat' => '{{%config_category}}'], 'c.category_id = cat.id')
            ->all();

        foreach ($configs as $config) {
            $this->update(
                '{{%config}}',
                ['category' => $config['name']],
                ['id' => $config['id']]
            );
        }

        $this->dropForeignKey('fk-config-category_id', '{{%config}}');
        $this->dropIndex('idx-config-category_id', '{{%config}}');
        $this->dropColumn('{{%config}}', 'category_id');
        $this->dropTable('{{%config_category}}');
    }
}
