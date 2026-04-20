<?php

use yii\db\Migration;

class m251026_070022_create_status_active extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%status_active}}', [
            'id' => $this->tinyInteger()->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'status_active' => $this->string(150)->notNull(),
        ]);

        // Seed example data
        $this->batchInsert('{{%status_active}}', ['id', 'status_active'], [
            [1, 'Active'],
            [2, 'Non Active'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%status_active}}');
    }
}