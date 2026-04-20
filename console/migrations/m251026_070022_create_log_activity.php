<?php

use yii\db\Migration;

class m251026_070022_create_log_activity extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%log_activity}}', [
            'id' => $this->primaryKey(),
            'controller_action' => $this->string(50)->notNull(),
            'model_name' => $this->string(50)->notNull(),
            'record_id' => $this->integer()->notNull(),
            'action_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'ip_address' => $this->string(45),
            'user_agent' => $this->string(255),
            'request_url' => $this->string(255),
            'before_data' => $this->text(),
            'after_data' => $this->text(),
            'status' => $this->string(50),
            'remarks' => $this->string(255),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%log_activity}}');
    }
}