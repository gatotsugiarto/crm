<?php

use yii\db\Migration;

/**
 * Who signs the SPH ("Diajukan Oleh"): free-text name and job title.
 * quotation_layout holds the default; each new quotation copies it (like the
 * other SPH texts) and can override it. When both are empty the PDF falls back to
 * the account's Assigned Sales name and job title.
 */
class m260929_110000_quotation_signer extends Migration
{
    public function up()
    {
        $this->addColumn('{{%quotation_layout}}', 'signer_name', $this->string(100)->null()->after('sign_right_label'));
        $this->addColumn('{{%quotation_layout}}', 'signer_title', $this->string(100)->null()->after('signer_name'));
        $this->addColumn('{{%quotation}}', 'signer_name', $this->string(100)->null()->after('closing_text'));
        $this->addColumn('{{%quotation}}', 'signer_title', $this->string(100)->null()->after('signer_name'));
    }

    public function down()
    {
        $this->dropColumn('{{%quotation}}', 'signer_title');
        $this->dropColumn('{{%quotation}}', 'signer_name');
        $this->dropColumn('{{%quotation_layout}}', 'signer_title');
        $this->dropColumn('{{%quotation_layout}}', 'signer_name');
    }
}
