<?php

use yii\db\Migration;

class m251026_090001_update_member_company_client_fk extends Migration
{
    public function safeUp()
    {
        // Drop FK jika sudah ada (untuk memastikan)
        $this->dropForeignKey('fk-member-company_id', '{{%member}}');
        $this->dropForeignKey('fk-member-client_id', '{{%member}}');

        // Alter column agar default tetap 1 (jika belum)
        $this->alterColumn('{{%member}}', 'company_id', $this->integer()->notNull()->defaultValue(1));
        $this->alterColumn('{{%member}}', 'client_id', $this->integer()->notNull()->defaultValue(1));

        // Tambahkan FK kembali
        $this->addForeignKey(
            'fk-member-company_id',
            '{{%member}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-member-client_id',
            '{{%member}}',
            'client_id',
            '{{%client}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-member-company_id', '{{%member}}');
        $this->dropForeignKey('fk-member-client_id', '{{%member}}');

        // Revert ke integer biasa (tanpa default)
        $this->alterColumn('{{%member}}', 'company_id', $this->integer()->notNull());
        $this->alterColumn('{{%member}}', 'client_id', $this->integer()->notNull());
    }
}
