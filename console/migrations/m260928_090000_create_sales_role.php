<?php

use yii\db\Migration;

/**
 * Adds a basic `sales` role: full create/update/delete on every Sales CRM menu,
 * read-only everywhere else.
 *
 * Read-only access is inherited from the existing `viewApplication` role (index +
 * view on every module). Write access is granted per sales controller through a
 * `backend.sales.<controller>.*` permission, which the controllers'
 * AccessControl matchCallback already accepts as covering every action of that
 * controller; each `.*` also gets its individual action permissions as children,
 * same layout as `backend.auth.member.*` etc.
 */
class m260928_090000_create_sales_role extends Migration
{
    /** controller id => action ids (Yii action ids, e.g. actionMarkSent -> mark-sent) */
    private $controllers = [
        'lead'                    => ['validate', 'index', 'view', 'create', 'update', 'delete', 'convert', 'reactive', 'nonactive'],
        'account'                 => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'contact'                 => ['validate', 'index', 'view', 'create', 'update', 'delete', 'setprimary', 'unsetprimary'],
        'accountaddress'          => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'opportunity'             => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'opportunityproduct'      => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'opportunitystagehistory' => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'activity'                => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'quotation'               => ['validate', 'index', 'view', 'create', 'update', 'approve', 'delete', 'reactive', 'nonactive'],
        'quotationitem'           => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'salesorder'              => ['validate', 'index', 'view', 'confirm', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'salesorderitem'          => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'invoice'                 => ['validate', 'index', 'view', 'pdf', 'mark-sent', 'mark-paid', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'invoiceitem'             => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
    ];

    public function up()
    {
        $auth = Yii::$app->authManager;

        $role = $auth->createRole('sales');
        $role->description = 'Sales';
        $auth->add($role);
        $auth->addChild($role, $auth->getRole('viewApplication'));

        // data = '2' marks it as a "basic role" so it shows up in the User form's Basic
        // Role dropdown (AuthItem::dropdownBasic()). Set via SQL because DbManager::add()
        // would serialize() it.
        $this->update('{{%auth_item}}', ['data' => '2'], ['name' => 'sales']);

        foreach ($this->controllers as $controller => $actions) {
            $prefix = "backend.sales.$controller";

            $controllerPerm = $auth->getPermission("$prefix.*");
            if ($controllerPerm === null) {
                $controllerPerm = $auth->createPermission("$prefix.*");
                $controllerPerm->description = '[Controller permission] .*';
                $auth->add($controllerPerm);
            }

            foreach ($actions as $action) {
                $actionPerm = $auth->getPermission("$prefix.$action");
                if ($actionPerm === null) {
                    $actionPerm = $auth->createPermission("$prefix.$action");
                    $actionPerm->description = "[Action permission] $prefix.$action";
                    $auth->add($actionPerm);
                }
                if (!$auth->hasChild($controllerPerm, $actionPerm)) {
                    $auth->addChild($controllerPerm, $actionPerm);
                }
            }

            $auth->addChild($role, $controllerPerm);
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // Only the role is removed; the backend.sales.* permissions stay since other
        // roles may have been given them after this migration ran.
        $auth->remove($auth->getRole('sales'));
    }
}
