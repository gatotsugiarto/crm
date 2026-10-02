<?php

use yii\db\Migration;

/**
 * Installation address on the Sales Order as free text (the site name and full
 * address, e.g. "PT. Hailal Sinar Cemerlang, Jl. Yosodipuro No. 31-33, ..."); empty =
 * the account's Shipping address. The address/contact pickers of
 * m261002_090000_sales_order_form stay in the table but are hidden in the form:
 * billing comes from the account's Billing address, contacts from its primary contact.
 */
class m261002_120000_sales_order_installation_text extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_order}}', 'installation_address', $this->text()->null()->after('pks_number'));
    }

    public function down()
    {
        $this->dropColumn('{{%sales_order}}', 'installation_address');
    }
}
