<?php

use yii\db\Migration;

class m251026_070002_create_client_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'client' => $this->string(150)->notNull(),
            'description' => $this->string(255),
            'status_id' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        // FK: company
        $this->addForeignKey(
            'fk_client_company',
            '{{%client}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // FK: status
        $this->addForeignKey(
            'fk_client_status',
            '{{%client}}',
            'status_id',
            '{{%status_active}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_client_status', '{{%client}}');
        $this->dropForeignKey('fk_client_company', '{{%client}}');
        $this->dropTable('{{%client}}');
    }
}
