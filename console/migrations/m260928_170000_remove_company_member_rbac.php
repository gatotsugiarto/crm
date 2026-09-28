<?php

use yii\db\Migration;

/**
 * Removes the RBAC items of the Master Company and Member screens, which were
 * deleted from the backend (their `company` / `member` tables never existed).
 *
 * Deleting an item through the authManager also drops its parent/child links
 * (e.g. superAdmin -> backend.auth.member.*, masterData -> masterCompany,
 * viewApplication -> backend.master.company.index/view) and assignments.
 * down() recreates exactly what is removed here.
 */
class m260928_170000_remove_company_member_rbac extends Migration
{
    private $memberActions = ['changepassword', 'create', 'delete', 'index', 'reactive', 'reset', 'suspend', 'update', 'validate', 'validatechangepassword', 'view'];
    private $companyActions = ['create', 'delete', 'deleteattachment', 'index', 'togglestatus', 'update', 'uploadattachment', 'validate', 'view'];

    public function up()
    {
        $auth = Yii::$app->authManager;
        $names = array_merge(
            ['memberAccess', 'masterCompany', 'backend.auth.member.*', 'backend.master.company.*'],
            array_map(fn($a) => "backend.auth.member.$a", $this->memberActions),
            array_map(fn($a) => "backend.master.company.$a", $this->companyActions)
        );
        foreach ($names as $name) {
            $item = $auth->getRole($name) ?? $auth->getPermission($name);
            if ($item !== null) {
                $auth->remove($item);
            }
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $groups = [
            'member'  => ['backend.auth.member', $this->memberActions, 'memberAccess', 'Member Access'],
            'company' => ['backend.master.company', $this->companyActions, 'masterCompany', 'Master Company'],
        ];
        foreach ($groups as [$prefix, $actions, $roleName, $roleDesc]) {
            $controller = $auth->createPermission("$prefix.*");
            $controller->description = '[Controller permission] .*';
            $auth->add($controller);

            $role = $auth->createRole($roleName);
            $role->description = $roleDesc;
            $auth->add($role);
            $this->update('{{%auth_item}}', ['data' => '1'], ['name' => $roleName]);

            foreach ($actions as $a) {
                $p = $auth->createPermission("$prefix.$a");
                $p->description = "[Action permission] $prefix.$a";
                $auth->add($p);
                $auth->addChild($controller, $p);
                $auth->addChild($role, $p);
            }
        }

        $auth->addChild($auth->getRole('superAdmin'), $auth->getPermission('backend.auth.member.*'));
        $auth->addChild($auth->getRole('masterData'), $auth->getRole('masterCompany'));
        $auth->addChild($auth->getRole('viewApplication'), $auth->getPermission('backend.master.company.index'));
        $auth->addChild($auth->getRole('viewApplication'), $auth->getPermission('backend.master.company.view'));
    }
}
