<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

class RbacController extends Controller
{
    public function actionInit()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // Permissions
        $manageUser = $auth->createPermission('manageUser');
        $manageUser->description = 'Kelola user backend';
        $auth->add($manageUser);

        $manageMember = $auth->createPermission('manageMember');
        $manageMember->description = 'Kelola member frontend';
        $auth->add($manageMember);

        $viewReport = $auth->createPermission('viewReport');
        $viewReport->description = 'Lihat laporan';
        $auth->add($viewReport);

        $exportData = $auth->createPermission('exportData');
        $exportData->description = 'Ekspor data';
        $auth->add($exportData);

        $accessPremium = $auth->createPermission('accessPremium');
        $accessPremium->description = 'Akses fitur premium';
        $auth->add($accessPremium);

        // Roles
        $superadmin = $auth->createRole('superadmin');
        $auth->add($superadmin);
        $auth->addChild($superadmin, $manageUser);
        $auth->addChild($superadmin, $manageMember);
        $auth->addChild($superadmin, $viewReport);
        $auth->addChild($superadmin, $exportData);
        $auth->addChild($superadmin, $accessPremium);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageUser);
        $auth->addChild($admin, $manageMember);
        $auth->addChild($admin, $viewReport);

        $staff = $auth->createRole('staff');
        $auth->add($staff);
        $auth->addChild($staff, $viewReport);

        $member = $auth->createRole('member');
        $auth->add($member);

        $premium = $auth->createRole('premium');
        $auth->add($premium);
        $auth->addChild($premium, $accessPremium);

        // Assign role ke user ID 1 sebagai superadmin
        $auth->assign($superadmin, 1);

        echo "RBAC seeder selesai.\n";
    }
}
