<?php

use yii\db\Migration;

/**
 * Sales Order as the MNC Play "SALES ORDER" form (printed by SalesorderController::actionPdf).
 *
 * - product.service_type (FTTHD / Metro-E / Other) and product.bandwidth (e.g. 100 Mbps):
 *   tick the Service Type boxes and fill Kapasitas/Bandwidth on the SO.
 * - sales_order: trial and contract periods, RFS date, PKS (contract) number, which
 *   account address / contact is used for installation and for billing (empty =
 *   the account's Shipping / Billing address and primary contact), and who confirmed
 *   it when (Dept. Head on the form; set by Confirm SO).
 * - RBAC: backend.sales.salesorder.pdf for Sales, Sales Manager (via .*) and viewApplication.
 */
class m261002_090000_sales_order_form extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}', 'service_type', $this->string(50)->null()->after('business_line'));
        $this->addColumn('{{%product}}', 'bandwidth', $this->string(50)->null()->after('service_type'));

        $this->addColumn('{{%sales_order}}', 'trial_start', $this->date()->null()->after('total_amount'));
        $this->addColumn('{{%sales_order}}', 'trial_end', $this->date()->null()->after('trial_start'));
        $this->addColumn('{{%sales_order}}', 'contract_start', $this->date()->null()->after('trial_end'));
        $this->addColumn('{{%sales_order}}', 'contract_end', $this->date()->null()->after('contract_start'));
        $this->addColumn('{{%sales_order}}', 'rfs_date', $this->date()->null()->after('contract_end')->comment('Ready For Service'));
        $this->addColumn('{{%sales_order}}', 'pks_number', $this->string(100)->null()->after('rfs_date')->comment('No PKS / contract number'));
        $this->addColumn('{{%sales_order}}', 'installation_address_id', $this->integer()->null()->after('pks_number'));
        $this->addColumn('{{%sales_order}}', 'installation_contact_id', $this->integer()->null()->after('installation_address_id'));
        $this->addColumn('{{%sales_order}}', 'billing_address_id', $this->integer()->null()->after('installation_contact_id'));
        $this->addColumn('{{%sales_order}}', 'billing_contact_id', $this->integer()->null()->after('billing_address_id'));
        $this->addColumn('{{%sales_order}}', 'confirmed_at', $this->dateTime()->null()->after('status'));
        $this->addColumn('{{%sales_order}}', 'confirmed_by', $this->integer()->null()->after('confirmed_at'));

        $this->addForeignKey('fk_so_installation_address', '{{%sales_order}}', 'installation_address_id', '{{%account_address}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_so_installation_contact', '{{%sales_order}}', 'installation_contact_id', '{{%contact}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_so_billing_address', '{{%sales_order}}', 'billing_address_id', '{{%account_address}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_so_billing_contact', '{{%sales_order}}', 'billing_contact_id', '{{%contact}}', 'id', 'SET NULL', 'CASCADE');

        $auth = Yii::$app->authManager;
        $pdf = $auth->getPermission('backend.sales.salesorder.pdf');
        if ($pdf === null) {
            $pdf = $auth->createPermission('backend.sales.salesorder.pdf');
            $pdf->description = '[Action permission] backend.sales.salesorder.pdf';
            $auth->add($pdf);
        }
        foreach ([$auth->getPermission('backend.sales.salesorder.*'), $auth->getRole('sales'), $auth->getRole('viewApplication')] as $parent) {
            if ($parent !== null && !$auth->hasChild($parent, $pdf)) {
                $auth->addChild($parent, $pdf);
            }
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $pdf = $auth->getPermission('backend.sales.salesorder.pdf');
        if ($pdf !== null) {
            $auth->remove($pdf);
        }

        foreach (['fk_so_installation_address', 'fk_so_installation_contact', 'fk_so_billing_address', 'fk_so_billing_contact'] as $fk) {
            $this->dropForeignKey($fk, '{{%sales_order}}');
        }
        foreach (['confirmed_by', 'confirmed_at', 'billing_contact_id', 'billing_address_id', 'installation_contact_id',
                  'installation_address_id', 'pks_number', 'rfs_date', 'contract_end', 'contract_start', 'trial_end', 'trial_start'] as $c) {
            $this->dropColumn('{{%sales_order}}', $c);
        }
        $this->dropColumn('{{%product}}', 'bandwidth');
        $this->dropColumn('{{%product}}', 'service_type');
    }
}
