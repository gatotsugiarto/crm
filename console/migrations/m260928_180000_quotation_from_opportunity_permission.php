<?php

use yii\db\Migration;

/**
 * Permission for QuotationController::actionCreateFromOpportunity (the "Create
 * Quotation" button on an opportunity): child of backend.sales.quotation.* (so
 * Sales Manager has it) and granted to the Sales role.
 */
class m260928_180000_quotation_from_opportunity_permission extends Migration
{
    const NAME = 'backend.sales.quotation.create-from-opportunity';

    public function up()
    {
        $auth = Yii::$app->authManager;
        $perm = $auth->createPermission(self::NAME);
        $perm->description = '[Action permission] ' . self::NAME;
        $auth->add($perm);
        $auth->addChild($auth->getPermission('backend.sales.quotation.*'), $perm);
        $auth->addChild($auth->getRole('sales'), $perm);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $auth->remove($auth->getPermission(self::NAME));
    }
}
