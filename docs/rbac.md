# Access Control (RBAC)

How permissions are named, checked and grouped into roles, and how to add a new role.

## How a request is authorized

Every module controller has the same `AccessControl` rule:

```php
'matchCallback' => function ($rule, $action) {
    $route   = 'backend.'.str_replace('/', '.', $this->getRoute());   // backend.sales.lead.update
    $parents = strstr($route, strrchr($route, '.'), true).'.*';       // backend.sales.lead.*
    if (Yii::$app->user->can($route) || Yii::$app->user->can($parents) || Yii::$app->user->can('root')) {
        return true;
    }
}
```

A logged-in user may run an action if they hold (directly or through a role):

1. the action permission `backend.<module>.<controller>.<action>`, or
2. the controller permission `backend.<module>.<controller>.*`, or
3. the `root` role.

Action ids are Yii's dashed ids: `actionMarkSent` becomes `mark-sent`.
If none match, Yii returns **403 Forbidden**. For AJAX calls the layout's global
`ajaxError` handler turns that into an on-page "You do not have permission to perform
this action." message.

`SiteController` and `MenuController` only require being logged in, and the module
landing cards in `backend/views/menu/<module>.php` are shown per `...<entity>.index`
permission.

## Permission items

- **Action permissions** (`type = 2`): `backend.<module>.<controller>.<action>`,
  description `[Action permission] <name>`.
- **Controller permissions** (`type = 2`): `backend.<module>.<controller>.*`,
  description `[Controller permission] .*`. Each has all of that controller's action
  permissions as children.
- They can be generated from the code through **User Management → RBAC → Permission
  → Create** (`RbacController::actionCreatepermission`, which uses
  `common/components/rbac/Pgenerator` to scan controllers). Migrations create them
  explicitly instead, so production gets the same items.

## Roles

| Role (`name`) | Shown as | Basic role | What it grants |
|---|---|---|---|
| `root` | root | | Everything, through the explicit `can('root')` bypass (no children) |
| `superAdmin` | Super Admin | ✅ | `adminApplication` + full User, RBAC, User Assignment, Member, Log Activity |
| `adminApplication` | Admin Application | ✅ | `masterData` (Application Setting, Master Company), `userAccess`, `userAssignment` |
| `viewApplication` | View Application | ✅ | Read-only: `index` + `view` on every master, productprice, sales and logdata controller, plus `backend.sales.account.downloadattachment` |
| `sales` | Sales | ✅ | `viewApplication` + `backend.sales.<14 controllers>.*` (full write on Sales CRM) |
| `productPricing` | Product-Pricing | ✅ | `viewApplication` + `backend.productprice.<7 controllers>.*` (full write on Product & Pricing) |
| `staff` | Staff Application | ✅ | Nothing yet (no children) |

Other `type = 1` items (`masterData`, `userAccess`, `userAssignment`, `rbac`,
`logActivity`, `memberAccess`, `applicationSetting`, `masterCompany`,
`userManagement`, `opportunityproduct`) are permission groups used as building blocks,
not assigned to users directly.

`sales` and `productPricing` were added by migrations
`m260928_090000_create_sales_role` and `m260928_100000_create_product_pricing_role`.

### Basic role

The **User** form (User Management → Edit User Access) has a single **Basic Role**
dropdown. It lists only roles with `auth_item.data = '2'`
(`AuthItem::dropdownBasic()`), and saving calls `User::saveRole()`, which **revokes
all of the user's assignments** and assigns the chosen role. So each user effectively
has one basic role.

## Adding a new role

Write it as a migration (so production gets it with `php yii migrate/up`). Copy
`console/migrations/m260928_100000_create_product_pricing_role.php`, which:

1. creates the role with a description;
2. adds `viewApplication` as a child if the role should see everything read-only;
3. sets `data = '2'` with `$this->update('{{%auth_item}}', ...)` so it appears in the
   Basic Role dropdown. Use raw SQL for this, because `DbManager::add()` would
   `serialize()` the data;
4. for each controller the role may write to, gets or creates
   `backend.<module>.<controller>.*` and its action children, and adds the `.*`
   permission to the role.

Then test locally with a throwaway assignment instead of a real user:

```bash
docker exec -i commcorp_php sh -c 'cd /var/www/html && php' <<'PHP'
<?php
define('YII_DEBUG', true); define('YII_ENV', 'dev');
require 'vendor/autoload.php'; require 'vendor/yiisoft/yii2/Yii.php';
require 'common/config/bootstrap.php'; require 'console/config/bootstrap.php';
$config = yii\helpers\ArrayHelper::merge(require 'common/config/main.php', require 'common/config/main-local.php',
    require 'console/config/main.php', require 'console/config/main-local.php');
new yii\console\Application($config);
$auth = Yii::$app->authManager; $uid = 99999;
$auth->assign($auth->getRole('sales'), $uid);
foreach (['backend.sales.lead.create', 'backend.productprice.product.update'] as $r) {
    $p = strstr($r, strrchr($r, '.'), true).'.*';
    echo $r, ': ', ($auth->checkAccess($uid, $r) || $auth->checkAccess($uid, $p)) ? 'ALLOW' : 'deny', "\n";
}
$auth->revoke($auth->getRole('sales'), $uid);
PHP
```

Finally, hand the user the equivalent SQL (`INSERT IGNORE` for items/children, the
`data = '2'` update, and the `migration` row) as described in
[deployment-workflow.md](deployment-workflow.md).

## Known gaps

- Buttons (New Data, Edit, Delete) are shown to everyone. Users without write access
  get the 403 message when they click them.
- `log_activity` stores password-hash changes and is readable by every role that
  inherits `viewApplication`. See [known-issues.md](known-issues.md).
