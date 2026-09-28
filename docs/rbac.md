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
| `superAdmin` | Super Admin | ✅ | `adminApplication` + full User, RBAC, User Assignment, Log Activity (no write access to Sales) |
| `adminApplication` | Admin Application | ✅ | `masterData` (Application Setting), `userAccess`, `userAssignment` |
| `viewApplication` | View Application | ✅ | Read-only: `index` + `view` on every master, productprice, sales and logdata controller, plus `backend.sales.account.downloadattachment` |
| `salesManager` | Sales Manager | ✅ | `viewApplication` + `backend.sales.<14 controllers>.*` (full write on Sales CRM: approve quotation, confirm SO, invoice mark sent/paid, delete) + `backend.sales.assign` |
| `sales` | Sales | ✅ | `viewApplication` + specific actions: create/update/reactive/nonactive on lead (incl. `convert`), account (incl. upload/download documents), address, contact (incl. set primary), activity, opportunity, opportunity product, quotation, quotation item; `delete` only on opportunity products and quotation items; `invoice.pdf`. Sales orders, invoices and stage history are read-only |
| `productPricing` | Product-Pricing | ✅ | `viewApplication` + `backend.productprice.<7 controllers>.*` (full write on Product & Pricing) |
| `staff` | Staff Application | ✅ | Nothing yet (no children) |

Other `type = 1` items (`masterData`, `userAccess`, `userAssignment`, `rbac`,
`logActivity`, `applicationSetting`, `userManagement`, `opportunityproduct`) are permission groups used as building blocks,
not assigned to users directly.

`sales` and `productPricing` were added by migrations
`m260928_090000_create_sales_role` and `m260928_100000_create_product_pricing_role`;
`m260928_160000_sales_manager_role` added `salesManager` and narrowed `sales`.

### Rules below the action level (`common/components/rbac/SalesAccess.php`)

Some limits can't be expressed as "may run this action", because the user is
allowed to open the edit form. These are checked in the models (and the forms hide
or lock the matching inputs):

- **Reassigning**: on an existing lead, account or opportunity, only users with
  `backend.sales.assign` (Sales Manager, root) may change `owner_user_id` (Sales
  Team) or `assigned_user_id` (Assigned Sales). Setting them while creating a record
  is open to everyone.
- **Approving**: moving a quotation into or out of `Approved` through the edit form
  needs `backend.sales.quotation.approve`, same as the Approve button (approval
  creates the Sales Order in a DB trigger).
- **Lead team**: editing, converting, (re)activating or deleting an existing lead is
  limited to members of the lead's Sales Team (`user.team_id = lead.owner_user_id`),
  the Sales Manager (`backend.sales.assign`; one manager covers several teams) and
  root, enforced in `LeadController::beforeAction` via `SalesAccess::leadTeamError()`.
  Others get 403 "This lead belongs to <team>. Only members of that team can
  convert/change it." A sales user without a team can't work on existing leads.
  Creating a lead for any team stays open. The layout's 403 handler shows this
  server message; the default AccessControl denial keeps the generic text.
- The Approve, Confirm SO and Mark Sent/Paid buttons are only shown to users who
  may use them.

`SalesAccess::can()` mirrors the controllers' check (action permission, its
controller's `.*`, or `root`) and allows everything outside a web request, so
console code and migrations are never blocked.

The **Team Leader** (`team.user_id`, Master Data → Sales Teams) and the **Sales
Manager** role are independent: being Team Leader grants nothing. The Sales Manager
role works across all teams (lead team checks and reassigning), so a Sales Manager
can leave Sales Team empty; a user can be Team Leader of only one active team.

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
