<?php

use yii\db\Migration;

/**
 * Splits sales access into two basic roles:
 *
 * - salesManager ("Sales Manager"): what `sales` could do until now, i.e. full
 *   write on all 14 Sales CRM controllers (approve quotation, confirm SO, invoice
 *   mark sent/paid, delete, ...) plus `backend.sales.assign` (change Sales Team /
 *   Assigned Sales on existing records, checked in the models).
 * - sales ("Sales"): day-to-day work. Create/edit leads (incl. Convert),
 *   accounts (incl. documents), addresses, contacts, activities, opportunities
 *   and their products, quotations and their items (up to Sent). Can remove line
 *   items (opportunity products, quotation items) but not master records.
 *   Sales orders, invoices and stage history are read-only (invoice PDF allowed).
 *
 * Both inherit viewApplication (read-only everywhere). Users keep their current
 * role; move team leaders to Sales Manager in User Management.
 */
class m260928_160000_sales_manager_role extends Migration
{
    private $salesControllers = [
        'lead', 'account', 'accountaddress', 'contact', 'opportunity', 'opportunityproduct',
        'opportunitystagehistory', 'activity', 'quotation', 'quotationitem', 'salesorder',
        'salesorderitem', 'invoice', 'invoiceitem',
    ];

    /** New `sales` grants: controller => action ids */
    private function salesActions()
    {
        $edit = ['validate', 'create', 'update', 'reactive', 'nonactive'];
        return [
            'lead'               => array_merge($edit, ['convert']),
            'account'            => array_merge($edit, ['uploadattachment', 'downloadattachment']),
            'accountaddress'     => $edit,
            'contact'            => ['validate', 'create', 'update', 'setprimary', 'unsetprimary'],
            'activity'           => $edit,
            'opportunity'        => $edit,
            'opportunityproduct' => array_merge($edit, ['delete']),
            'quotation'          => $edit,
            'quotationitem'      => array_merge($edit, ['delete']),
            'invoice'            => ['pdf'],
        ];
    }

    public function up()
    {
        $auth = Yii::$app->authManager;
        $view = $auth->getRole('viewApplication');

        // Sales Manager
        $manager = $auth->createRole('salesManager');
        $manager->description = 'Sales Manager';
        $auth->add($manager);
        $this->update('{{%auth_item}}', ['data' => '2'], ['name' => 'salesManager']);
        $auth->addChild($manager, $view);
        foreach ($this->salesControllers as $c) {
            $auth->addChild($manager, $this->perm("backend.sales.$c.*", '[Controller permission] .*'));
        }
        $assign = $this->perm('backend.sales.assign', '[Custom permission] change Sales Team / Assigned Sales on existing records');
        $auth->addChild($manager, $assign);

        // Sales: drop controller-wide grants, add the specific actions
        $sales = $auth->getRole('sales');
        foreach ($this->salesControllers as $c) {
            $p = $auth->getPermission("backend.sales.$c.*");
            if ($p !== null && $auth->hasChild($sales, $p)) {
                $auth->removeChild($sales, $p);
            }
        }
        foreach ($this->salesActions() as $c => $actions) {
            foreach ($actions as $a) {
                $name = "backend.sales.$c.$a";
                $p = $this->perm($name, "[Action permission] $name");
                if (!$auth->hasChild($sales, $p)) {
                    $auth->addChild($sales, $p);
                }
            }
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $sales = $auth->getRole('sales');
        foreach ($this->salesActions() as $c => $actions) {
            foreach ($actions as $a) {
                $p = $auth->getPermission("backend.sales.$c.$a");
                if ($p !== null && $auth->hasChild($sales, $p)) {
                    $auth->removeChild($sales, $p);
                }
            }
        }
        foreach ($this->salesControllers as $c) {
            $p = $auth->getPermission("backend.sales.$c.*");
            if ($p !== null && !$auth->hasChild($sales, $p)) {
                $auth->addChild($sales, $p);
            }
        }

        $auth->remove($auth->getRole('salesManager'));
        $auth->remove($auth->getPermission('backend.sales.assign'));
    }

    /** Get or create a permission item. */
    private function perm($name, $description)
    {
        $auth = Yii::$app->authManager;
        $p = $auth->getPermission($name);
        if ($p === null) {
            $p = $auth->createPermission($name);
            $p->description = $description;
            $auth->add($p);
        }
        return $p;
    }
}
