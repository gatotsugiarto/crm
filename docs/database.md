# Database

Schema reference for `crm_prod` (production, MySQL 5.5) / `commcorp_tb` (local,
MariaDB 10.11). 37 tables, 2 stored procedures, 19 triggers, no views or events.

Most business rules for the sales pipeline are implemented **in triggers and stored
procedures**, not in PHP. This page lists all of them; [sales-flow.md](sales-flow.md)
explains how they chain together.

## Tables by area

Common columns omitted below: `id`, `status_id` (FK `status_active`: 1 Active, 2 Non
Active), `created_at`, `created_by`, `updated_at`, `updated_by`.

### Sales

| Table | Key columns | Notes |
|---|---|---|
| `lead` | company_name, contact_name, email, phone, lead_source, industry, address, city/province/country/postal_code_id, **is_converted**, converted_account_id, converted_contact_id, owner_user_id → team | Converted by `sp_convert_lead_to_customer` |
| `account` | parent_account_id → account (holding/group), code (unique), name, **account_type** enum(Prospect, Customer, Partner, Reseller, Vendor), industry, tax_number, contact info, location FKs, price_list_id, owner_user_id → team | |
| `account_address` | account_id, **address_type** enum(Invoice, Branch), address, location FKs | Billing/branch addresses |
| `contact` | account_id, fullname, job_title, email, phone, mobile, **is_primary** | One primary per account, via `sp_set_primary_contact` |
| `opportunity` | account_id, contact_id, owner_user_id → team, name, **stage** enum(Prospecting, Qualification, Proposal, Negotiation, Closed Won, Closed Lost), amount, close_date, probability | `amount` maintained by triggers |
| `opportunity_product` | opportunity_id, product_id, qty, price, discount, **total (generated: `qty*price-discount`, STORED)**, is_upsell, parent_product_id → opportunity_product | |
| `opportunity_stage_history` | opportunity_id, old_stage, new_stage, changed_at, changed_by → user, description, days_in_previous_stage | Written only by triggers |
| `activity` | account_id, contact_id, opportunity_id, reference_type/reference_id, assigned_to → team, **activity_type** enum(Call, Meeting, Email, Task, Note), priority enum(Low, Normal, High, Urgent), subject, activity_date, due_date, reminder_at, is_completed, completed_at, outcome | |
| `quotation` | quotation_number, account_id, opportunity_id, quotation_date, valid_until, total_amount, **status** enum(Draft, Sent, Approved, Rejected) | `total_amount` maintained by triggers |
| `quotation_item` | quotation_id, product_id, qty, price, discount, total | `total` computed by trigger |
| `sales_order` | order_number, account_id, quotation_id, order_date, total_amount, **status** enum(Draft, Confirmed, Completed, Cancelled) | Created by trigger on quotation approval |
| `sales_order_item` | sales_order_id, product_id, qty, price, discount, total | `total` computed by trigger |
| `invoice` | invoice_number, account_id, sales_order_id, invoice_date, due_date, total_amount, **status** enum(Draft, Sent, Paid, Overdue) | Created in PHP by `SalesorderController::actionConfirm` |
| `invoice_item` | invoice_id, product_id, qty, price, discount, total | Copied from SO items in PHP |
| `record_owner_history` | entity_type, entity_id, old_owner_id, new_owner_id, changed_at, changed_by | Written by `trg_lead_owner_update` |

### Product & Pricing

| Table | Key columns | Notes |
|---|---|---|
| `product` | code, name, category_id, **parent_product_id** → product, uom_id, **type** enum(Goods, Service, Subscription, Bundle, Software), customer_type, revenue_model, **bundle_price_type** enum(fixed, sum), base_price, **is_bundle_expand** | Field rules: [product-classification.md](product-classification.md) |
| `product_category` | name, description | Hardware / Software / Service |
| `product_uom` | name, code | License, Package, Unit, Subscription, Series |
| `product_bundle_item` | bundle_product_id → product, product_id → product, quantity | Components of a Bundle product |
| `price_list` | name, currency | e.g. Retail vs Corporate |
| `product_price` | product_id, price_list_id, price, valid_from, valid_to, min_qty | Date-ranged price per price list |
| `product_discount` | product_id, price_list_id, discount_type enum(percent, amount), discount_value, priority, min_qty, valid_from, valid_to, is_stackable | |

Bundle pricing intent (from the design notes): `fixed` means use the bundle's own
`product_price`; `sum` means add up component prices from `product_bundle_item`.
`is_bundle_expand = 1` means expand a bundle into its items on documents, else keep
one line. The Product & Pricing module that maintains these tables is complete, but
**none of the pricing tables are read by the sales screens yet.** Prices
on opportunity/quotation items are entered manually (see
[known-issues.md](known-issues.md)).

### Master data

| Table | Notes |
|---|---|
| `country` → `province` → `city` (type enum CITY/REGENCY) → `postal_code` | Location hierarchy used by lead/account/address |
| `team` | name, description, **user_id** (manager) → user. `user.team_id` → team |
| `application_setting` | `default_password` for new/reset users. Also has leftover payroll columns (`payroll_period`, `hr_default_password`) |
| `status_active` | 1 Active, 2 Non Active |
| `status` | 1 Yes, 2 No |

### Auth and audit

| Table | Notes |
|---|---|
| `user` | username, fullname, auth_key, password_hash, email, status (10 = active), team_id. `created_at/updated_at` are **unix ints** here, unlike the datetime columns elsewhere |
| `auth_item`, `auth_item_child`, `auth_assignment`, `auth_rule` | Yii DbManager RBAC, see [rbac.md](rbac.md) |
| `log_activity` | Audit log from `LoggableBehavior`: controller_action, model_name, record_id, action_by, ip_address, user_agent, request_url, before_data/after_data (JSON), status, remarks |
| `migration` | Yii migration history |

The migrations for `member`, `company` and `client` exist in `console/migrations`,
but **those tables do not exist** in production or local. The Master Company and
Member screens will error if opened.

## Stored procedures

### `sp_convert_lead_to_customer(IN p_lead_id, IN p_user_id, OUT p_account_id, OUT p_contact_id, OUT p_opportunity_id)`

Called by `LeadController::actionConvert`. In one transaction:

1. Locks the lead (`FOR UPDATE`) where `is_converted = 0`; if none,
   `SIGNAL '45000' 'Lead not found or already converted'`.
2. Inserts an `account` (type `Prospect`) from the lead's company/location fields.
   Owner = lead owner, or the converting user if the lead has none.
3. Inserts a primary `contact` from the lead's contact fields.
4. Inserts an `opportunity` "Opportunity - {company}" at stage `Prospecting`,
   probability 10, amount 0, close date today + 30 days.
5. Marks the lead `is_converted = 1` with the new account/contact ids.

### `sp_set_primary_contact(IN p_contact_id, IN p_user_id)`

Called by `ContactController::actionSetprimary`. Resets `is_primary = 0` for every
contact of the same account, then sets it to 1 on the chosen contact.

## Triggers

| Table | Trigger | Timing | What it does |
|---|---|---|---|
| `lead` | `trg_lead_owner_update` | BEFORE UPDATE | If `owner_user_id` changed, insert a row into `record_owner_history` |
| `opportunity` | `tr_opp_stage_insert` | AFTER INSERT | Insert initial `opportunity_stage_history` row (old_stage NULL) |
| `opportunity` | `tr_opp_stage_update` | AFTER UPDATE | If `stage` changed, insert a history row. `days_in_previous_stage` is computed from the opportunity's `created_at`, not from the previous stage change |
| `opportunity_product` | `tr_update_opp_amount_after_upsert` / `_after_update` / `_after_delete` | AFTER INSERT/UPDATE/DELETE | Recompute `opportunity.amount = SUM(total)` of its products |
| `quotation` | `trg_quotation_after_update_status` | AFTER UPDATE | Status → `Sent`: set the opportunity to stage `Proposal`, probability 50 |
| `quotation` | `trg_quotation_to_sales_order` | AFTER UPDATE | Status → `Approved`: set the opportunity to `Closed Won`, probability 100, amount = quotation total; if no SO exists for this quotation, insert a `sales_order` (`SO/YYYYMMDD/NNNN`, status Draft) and let `trg_so_copy_items` copy the items (the trigger's own copy was removed in `m260928_120000`, it duplicated them) |
| `quotation_item` | `trg_qtn_item_before_insert` / `_before_update` | BEFORE INSERT/UPDATE | `total = qty*price - discount` |
| `quotation_item` | `trg_qtn_after_insert` / `_after_update` / `_after_delete` | AFTER INSERT/UPDATE/DELETE | Recompute `quotation.total_amount = SUM(total)` |
| `sales_order` | `trg_so_copy_items` | AFTER INSERT | Copy the linked quotation's items into `sales_order_item`. The only place SO items are copied, for approvals and for SOs created by hand |
| `sales_order_item` | `trg_so_item_before_insert` / `_before_update` | BEFORE INSERT/UPDATE | `total = qty*price - discount` |
| `team` | `tr_team_before_insert_check_manager` | BEFORE INSERT | Reject if the manager user already manages another active team |
| `team` | `tr_team_after_upsert_manager` | AFTER INSERT | Set the manager's `user.team_id` to this team |
| `team` | `tr_team_after_update_manager` | AFTER UPDATE | If the manager changed, set the new manager's `user.team_id` |

There are no triggers on `invoice` / `invoice_item`; invoice totals are copied from
the Sales Order in PHP.

## Gotchas when importing or migrating

- **Generated column.** `opportunity_product.total` is `GENERATED ALWAYS ... STORED`.
  Navicat dumps include a value for it, which MariaDB rejects in strict mode
  (`ERROR 1906`). Import with a non-strict session, see
  [local-development.md](local-development.md).
- **Triggers need privileges.** Loading a dump with triggers/procedures needs a user
  with `TRIGGER` / `CREATE ROUTINE` (locally: `root`).
- **Triggers fire on raw SQL too.** A manual `UPDATE quotation SET status='Approved'`
  creates a Sales Order exactly like the UI does. Test data changes on a copy.
- **Trigger DEFINER.** A trigger created by a migration gets the migration user as
  DEFINER (`commcorp_2026_tb@%` locally) and runs with that user's privileges.
  When copying the DB into a scratch database for tests, strip the `DEFINER=...`
  clauses from the dump (the `sed` in [local-development.md](local-development.md))
  or the triggers fail with ERROR 1142.
- **Production is MySQL 5.5.** Avoid syntax it lacks (JSON type, CTEs, window
  functions, `ALTER TABLE ... RENAME COLUMN`) in migrations.
