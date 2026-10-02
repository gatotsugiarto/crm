# Known Issues

Problems found while reviewing the codebase and database (2026-09-28). None of these
are fixed yet unless marked. Ordered roughly by impact.

## 1. ~~Approving a quotation duplicates Sales Order items~~ (fixed 2026-09-28)

Approving a quotation created every Sales Order item twice (and the invoice lines
after Confirm), because two triggers copied the same items:
`trg_quotation_to_sales_order` inserted the SO and copied the items, then
`trg_so_copy_items` fired on that insert and copied them again.

Fixed by migration `m260928_120000_fix_duplicate_sales_order_items`: the quotation
trigger no longer copies items. `trg_so_copy_items` stays, because Sales Orders
created by hand (SO form with a quotation selected) also rely on it. Rows already
duplicated on production are not cleaned up automatically; the STEP 1 query in
`scripts/production/2026-09-28_fix_duplicate_so_items.sql` lists them.

## 2. Gii and debug mode are exposed on production

- `backend/web/index.php` is **tracked in git** with `YII_DEBUG = true` and
  `YII_ENV = 'dev'`, so every `git pull` deploys debug mode: full stack traces with
  file paths and SQL on errors.
- `backend/config/main.php` registers the **Gii** module unconditionally with
  `'allowedIPs' => [..., '*']`. Locally `/gii` answers 200 **without logging in**.
  If production behaves the same, anyone who can reach the server can open Gii's code
  generator, which can write PHP files into the app.

Check on production: open `https://<prod-host>/gii` in a private window. Fix: move
Gii (and the debug module) into `backend/config/main-local.php` for dev only, and use
the `environments/prod` `index.php` on the server (`php init --env=Production`), or
stop tracking `backend/web/index.php`.

## 3. Delete and status actions accept GET

33 of the 38 module controllers have no `VerbFilter`, so `/<module>/<entity>/delete?id=N` deletes on a
plain GET (a link, an image tag, a prefetch). Yii's CSRF check only applies to POST.
Only the auth/logdata controllers restrict `delete` to POST. Fix: add
`VerbFilter` with `delete`, `nonactive`, `reactive`, `approve`, `confirm`,
`mark-sent`, `mark-paid`, `convert`, `setprimary` → `['POST']`, after checking each
view calls them with POST (the grid JS already does for delete).

## 4. Password hashes are readable in the audit log

`ChangePasswordUser::changePassword()` writes `password_hash` before/after into
`log_activity`, and every role inheriting `viewApplication` (including `sales`,
`productPricing`) can read Log Activity. Bcrypt hashes are slow to crack, but they
should not be exposed. Fix: log that the password changed without the hash values,
and consider removing `logdata` from `viewApplication`.

## 5. ~~Missing tables: `company`, `member`, `client`~~ (removed 2026-09-28)

The Master Company and Member screens, whose tables never existed, were deleted
from the backend together with their RBAC items
(`m260928_170000_remove_company_member_rbac`). `common/models/Member` stays because
the unused `frontend/` app still references it; the old create-table migrations stay
as history.

## 6. Sales screens don't use the pricing data yet

The Product & Pricing module is **complete** (signed off 2026-09-28): products,
categories, UOM, bundles, price lists, date-ranged prices and discounts are all
maintained there. What is still missing is on the **Sales** side: opportunity and
quotation item prices are typed in by hand, and nothing reads `product_price`,
`product_discount`, bundle pricing (`fixed` / `sum`, `is_bundle_expand`) or
`account.price_list_id`. Treat this as a future Sales feature ("suggest price from
the account's price list"), not as unfinished Product & Pricing work.

## Fixed 2026-09-28 (Account BRD work)

- **Account form could not save.** `Account` validated `status_id` against a
  non-existent class `ActiveStatus` (and its `getStatus()` relation pointed to a
  non-existent `StatusActive` in the wrong namespace), so any create/edit through the
  form threw "Class not found". The three existing accounts came from lead conversion
  (stored procedure), which bypasses model validation.
- **Sales Team validated against the wrong table.** `owner_user_id` holds a team id
  but was validated with `exist` against `user`; it only passed because ids happened
  to overlap.

## Fixed 2026-09-28 (forms)

- Location dropdowns on lead / account / account address now depend on each other
  and the chain is validated server-side; email fields on lead, account and contact
  are validated as email addresses.
- The team's `user_id` is labelled **Team Leader** (was "Manager"), to keep it apart
  from the **Sales Manager** role.

## Fixed 2026-09-30 (Price List)

- Deleting a price list that still had product prices / discounts threw a 500
  ("Request failed"); one used only by accounts (no FK on `account.price_list_id`)
  was deleted and left those accounts pointing at nothing. Delete now refuses with
  what still uses it. Price lists can be set **Non Active** / reactivated from the
  Status column; Non Active lists drop out of the Account, Product Price and
  Discount forms (`PriceList::dropdownActive`, which keeps a record's current list).
- The Product Bundles menu card checked the Price List permission.

## Fixed 2026-09-28 (deleting)

- Deleting an account failed with "Request failed" whenever it had contacts (FK
  RESTRICT), and addresses were left orphaned (FK SET NULL). Account delete now
  removes contacts, addresses and documents first and explains what blocks it
  otherwise. The FKs themselves are unchanged, so a raw SQL `DELETE FROM account`
  still behaves the old way.
- Converted leads can no longer be deleted (kept as history).

## Smaller issues

- **`owner_user_id` points to `team.id`**, not `user.id` (lead, account, opportunity;
  also `activity.assigned_to`). Easy to join wrongly in reports.
- **`days_in_previous_stage`** in `tr_opp_stage_update` is `DATEDIFF(NOW(),
  opportunity.created_at)`, i.e. days since the opportunity was created, not since
  the previous stage change.
- **SO number generation** in the trigger uses `MAX(...) + 1` for today's orders
  without locking. Two approvals at the same moment could get the same number.
- **Stage history `changed_by`** comes from `NEW.updated_by`. The quotation triggers
  update the opportunity without setting `updated_by`, so automatic stage changes are
  attributed to whoever last edited the opportunity.
- **Invoice totals** are not recomputed when invoice items are edited (no triggers on
  invoice tables), and nothing sets `Overdue` automatically.
- **Action buttons are not permission-aware**. Users without write access see
  New/Edit/Delete and get the 403 message on click (message added 2026-09-28).
- **Dead code**: `frontend/` (template, not served), `backend/modules/auth/views/rbac
  copy/` (26 tracked files), `application_setting.payroll_period` /
  `hr_default_password` (payroll leftovers), commented-out Reactive/Nonactive actions
  in several controllers, `SiteController::actionTest`, and the root
  `docker-compose.yml`.
- **Dashboard** (`site/index`) is empty; the in-app Documentation page is the only
  landing content. `CRM.txt` (in `common/modules/productprice/models/`) lists planned
  Reports (pipeline, revenue, product sales, customer activity) that don't exist yet.

## 7. Recurring deals are invoiced once (open, 2026-10-02)

Confirm SO creates **one** invoice for the whole Sales Order total. That fits OTC
deals, but a **Recurring** deal is billed every month over the contract
(`sales_order.contract_start` / `contract_end`). Not built yet, pending a decision:
recurring billing in this CRM (monthly invoices generated over the contract) or in
the separate billing system (the SO's "Kuning (Billing)" copy), in which case the
CRM invoice would cover only the one-time items. See the Revenue Type table in
[sales-flow.md](sales-flow.md#1-lead).
