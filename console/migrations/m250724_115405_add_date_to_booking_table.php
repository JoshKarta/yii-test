<?php

use yii\db\Migration;

class m250724_115405_add_date_to_booking_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%booking}}', 'date', $this->date()->notNull()->after('description'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%booking}}', 'date');
    }
}
