# Architecture

How the commcorp CRM is put together: stack, directory layout, modules, and the
coding patterns every CRUD screen follows.

## Stack

| Layer | What |
|---|---|
| Framework | Yii 2 Advanced Project Template (`yiisoft/yii2 ~2.0.45`), PHP 8.2 in Docker (composer allows `>=7.4`) |
| UI | Bootstrap 4 (`yii2-bootstrap4`) on the "Light Bootstrap Dashboard" theme, Font Awesome 6, jQuery + Pjax |
| Widgets | Kartik: `yii2-grid` (GridView + export), `select2`, `datecontrol`, `number`, `fileinput`, `tree-manager` |
| Database | MySQL 5.5 in production (`crm_prod`), MariaDB 10.11 locally (`commcorp_tb`) |
| RBAC | `yii\rbac\DbManager` (`auth_item`, `auth_item_child`, `auth_assignment`, `auth_rule`) |
| Locale | `id_ID`, currency IDR, dates shown `d-m-Y` / stored `Y-m-d` (kartik `datecontrol`) |

Only the **backend** app is used. `frontend/` is unmodified template boilerplate, and
the local nginx only serves `backend/web` (port 8086).

## Directory layout

```
backend/
  config/main.php          modules registry, user/session, formatter, datecontrol
  controllers/             SiteController (login, dashboard, documentation page),
                           MenuController (module landing pages + change password)
  modules/<module>/controllers/   one controller per entity (see below)
  modules/<module>/views/<entity>/  index.php (grid + modals + page JS), _form.php, view.php, _search.php
  views/layouts/main.php   sidebar, top bar, #alert-container, global JS (403 handler)
  views/menu/<module>.php  module landing pages: card grid, one card per entity,
                           each gated by `user->can('backend.<module>.<entity>.index')`
  views/site/documentation.php   in-app user documentation (Documentation menu)
common/
  modules/<module>/models/ ActiveRecord models + *Search models (shared across apps)
  components/behaviors/    LoggableBehavior (audit log), TokenProtectedFormBehavior
  components/rbac/Pgenerator.php  scans controllers to generate permission items
  config/params.php        user.passwordDefault, user.passwordMinLength, ...
console/
  migrations/              schema + RBAC migrations (see deployment-workflow.md)
  controllers/RbacController.php  `yii rbac/init` (template bootstrap)
scripts/
  dump-db.sh, php-dump.php DB dump helpers (password via MYSQL_PWD)
  production/YYYY-MM-DD_*.sql  SQL handed to the user to run on production (gitignored)
docker/                    local dev: php-fpm 8.2, nginx, MariaDB (see local-development.md)
```

Models live in `common/modules/...` while controllers/views live in
`backend/modules/...`; the two trees mirror each other by module name.

## Modules

| Module | Route prefix | Entities (controller ids) |
|---|---|---|
| `auth` | `/auth/...` | `user`, `userassignment`, `rbac` (roles/permissions) |
| `master` | `/master/...` | `applicationsetting`, `country`, `province`, `city`, `postalcode`, `team`, `location` (JSON lookups for the dependent location dropdowns) |
| `productprice` | `/productprice/...` | `product`, `productcategory`, `productuom`, `productbundleitem`, `pricelist`, `productprice`, `productdiscount` |
| `sales` | `/sales/...` | `lead`, `account`, `accountaddress`, `contact`, `opportunity`, `opportunityproduct`, `opportunitystagehistory`, `activity`, `quotation`, `quotationitem`, `salesorder`, `salesorderitem`, `invoice`, `invoiceitem` |
| `logdata` | `/logdata/...` | `logactivity` (audit log viewer) |

Each module also has a `default` controller (landing), and the sidebar links to
`/menu/<module>` pages (`MenuController`) which show one card per entity.

For what each sales entity means and how records flow between them, see
[sales-flow.md](sales-flow.md). For product field rules, see
[product-classification.md](product-classification.md).

## The standard CRUD pattern

Almost every entity controller is Gii-generated and then adapted the same way. When
adding a new entity, copy an existing one (e.g. `ProductuomController` + its views)
rather than starting from Gii defaults.

**Controller** (`backend/modules/<m>/controllers/<X>Controller.php`)
- `behaviors()` has only `AccessControl` with the shared `matchCallback` (see
  [rbac.md](rbac.md)). Most controllers have **no `VerbFilter`**, so actions
  like `delete` also answer GET (see [known-issues.md](known-issues.md)).
- `actionCreate` / `actionUpdate` serve two modes:
  - AJAX GET → `renderAjax('_form')` into the page's `#appModal`, with a fresh form token.
  - AJAX POST → JSON `{success, message}` or `{success:false, errors}`.
  - Non-AJAX fallback renders/redirects normally with a session flash.
- `actionDelete` does a hard `$model->delete()` and returns JSON for AJAX.
- `actionNonactive` / `actionReactive` flip `status_id` between 2 and 1
  (soft enable/disable, see "status_id" below).
- `actionValidate` is used for AJAX ActiveForm validation where enabled.

**Views** (`backend/modules/<m>/views/<x>/`)
- `index.php` holds the Kartik GridView (inside Pjax container `#w0-pjax`), the
  "New Data" button (`.create-data`, `data-url`), row buttons (`.edit-data`,
  `.view-data`, `.delete-js`, `.nonactive-js`, `.reactive-js`), the Bootstrap modals
  (`#appModal`, `#viewModal`, `#confirmDeleteModal`, `#confirmActiveNonActiveModal`)
  and the page's own JS in a `registerJs(<<<JS ... JS)` block at the bottom.
- The JS loads forms with `$('#appModal .modal-body').load(url)`, submits with
  `$.post(...)` on ActiveForm's `beforeSubmit`, then `$.pjax.reload('#w0-pjax')` and
  writes a Bootstrap alert into `#alert-container` (defined in the layout).
- Buttons are **not** hidden by permission; the server enforces access. A 403 on
  any AJAX call is caught globally in `views/layouts/main.php` (closes the modal,
  shows "You do not have permission to perform this action.").

**Models** (`common/modules/<m>/models/`)
- Standard `TimestampBehavior` / `BlameableBehavior` for `created_at/by`,
  `updated_at/by` (datetime columns, user id).
- `LoggableBehavior` (every business model) writes an audit row into `log_activity`
  on insert/update/delete with JSON before/after snapshots of the changed fields.
  Set `$model->disableLog = true` to skip, or `extraRemarks` for a custom remark.
- `TokenProtectedFormBehavior` (attached as `tokenProtection`) guards against double
  submit: the controller calls `generateToken()` when rendering the form and
  `consumeToken()` after a successful save; a POST with a stale `form_token` fails
  validation with "Invalid or duplicate submission." Actions that save without a
  form (status toggles, mark-sent, set-primary) call
  `$model->detachBehavior('tokenProtection')` first.
- `*Search` models provide the grid filtering.

## Conventions worth knowing

- **`status_id`** on almost every table is an FK to `status_active`
  (1 = Active, 2 = Non Active). It is an enable/disable flag, separate from any
  business status column (e.g. `quotation.status` = Draft/Sent/Approved/Rejected).
  `status` table (1 = Yes, 2 = No) is a generic yes/no lookup.
- **`owner_user_id`** on `lead`, `account`, `opportunity` (and `activity.assigned_to`)
  references **`team.id`**, not `user.id`, despite the name. A team has one manager
  (`team.user_id`), and `user.team_id` points back to the team.
- **Heavy logic lives in the database.** Opportunity amount rollups, quotation totals,
  Sales Order creation on quotation approval, stage history, and lead conversion are
  MySQL triggers / stored procedures, not PHP. Read [database.md](database.md) before
  changing any of those flows, because PHP code often relies on a trigger side effect
  (e.g. `QuotationController::actionApprove` only sets `status = 'Approved'` and lets
  the trigger create the Sales Order).
- **Code is mostly English, comments/flash messages are partly Indonesian.** Keep new
  UI strings in English to match the existing screens.
- **Location fields** (country, province, city, postal code) on lead, account and
  account address are dependent dropdowns: the parent select carries
  `data-dep-child` / `data-dep-url`, a global handler in `layouts/main.php` refills
  the child from `master/location/*`, and `common/components/LocationRules`
  validates the chain server-side. New forms preselect the country when only one
  exists.
- **Date pickers in modals**: the theme hides every `.dropdown-menu` unless it is
  inside `.show`, which made the Kartik/bootstrap-datepicker popup (appended to
  `<body>`) invisible; `web/css/custom-app.css` re-shows
  `.datepicker.datepicker-dropdown.dropdown-menu`. `DateControl` needs an explicit
  `widgetClass` (the module has `autoWidget` off) and a picker `format` matching its
  `displayFormat` (e.g. `dd-mm-yyyy` for `php:d-m-Y`).
- **New User default password** comes from `application_setting.default_password` if
  that row exists, else `params['user.passwordDefault']` (`12345678`).
