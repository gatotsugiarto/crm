<?php

use yii\db\Migration;

class m251026_070001_create_company_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull(),
            'company_name' => $this->string(150)->notNull(),
            'description' => $this->string(255),
            'status_id' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        // FK: status
        $this->addForeignKey(
            'fk_company_status',
            '{{%company}}',
            'status_id',
            '{{%status_active}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Default Seed
        $this->insert('{{%company}}', [
            'code' => 'SP',
            'company_name' => 'Satu Payroll',
            'status_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }

    public function down()
    {
        $this->dropForeignKey('fk_company_status', '{{%company}}');
        $this->dropTable('{{%company}}');
    }
}
