<?php

use yii\db\Migration;

/**
 * Adds a basic `productPricing` role: full create/update/delete on every
 * Product & Pricing menu, read-only everywhere else.
 *
 * Same layout as the `sales` role (m260928_090000_create_sales_role): read-only
 * comes from `viewApplication`, write access from one
 * `backend.productprice.<controller>.*` permission per controller.
 */
class m260928_100000_create_product_pricing_role extends Migration
{
    /** controller id => action ids */
    private $controllers = [
        'product'           => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'productcategory'   => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'productuom'        => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'productbundleitem' => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'pricelist'         => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'productprice'      => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
        'productdiscount'   => ['validate', 'index', 'view', 'create', 'update', 'delete', 'reactive', 'nonactive'],
    ];

    public function up()
    {
        $auth = Yii::$app->authManager;

        $role = $auth->createRole('productPricing');
        $role->description = 'Product-Pricing';
        $auth->add($role);
        $auth->addChild($role, $auth->getRole('viewApplication'));

        // data = '2' marks it as a "basic role" so it shows up in the User form's Basic
        // Role dropdown (AuthItem::dropdownBasic()). Set via SQL because DbManager::add()
        // would serialize() it.
        $this->update('{{%auth_item}}', ['data' => '2'], ['name' => 'productPricing']);

        foreach ($this->controllers as $controller => $actions) {
            $prefix = "backend.productprice.$controller";

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

        // Only the role is removed; the backend.productprice.* permissions stay since
        // other roles may have been given them after this migration ran.
        $auth->remove($auth->getRole('productPricing'));
    }
}
