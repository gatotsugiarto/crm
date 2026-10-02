# Local Development

Running the app locally, refreshing the local database from a production dump, and
running migrations.

## Docker stack

Defined in `docker/docker-compose.yml` (project name `commcorp`):

| Container | Image | Purpose |
|---|---|---|
| `commcorp_php` | `docker/php/Dockerfile` (php:8.2-fpm + pdo_mysql, intl, gd, zip, imagick, composer) | Runs the app; repo mounted at `/var/www/html` |
| `commcorp_nginx` | nginx | Serves `backend/web` on **http://localhost:8086** |
| `commcorp_db` | mariadb:10.11 | DB `commcorp_tb`, host port **3309**, app user `commcorp_2026_tb`, root password in the compose file. Data in external volume `commcorp_db_data` |

```bash
cd docker && docker compose up -d
```

The app connects with `mysql:host=commcorp_db;dbname=commcorp_tb` from
`common/config/main-local.php`. The `*-local.php` config files are gitignored and hold
real credentials; that is expected (see CLAUDE.md).

The root `docker-compose.yml` is the unused Yii template one; use `docker/`.

## Refreshing the local DB from production

Production is reachable only by the user. Claude cannot connect to it. The user
exports a dump from Navicat (structure + data, with procedures and triggers) and puts
it somewhere local, e.g. `~/Downloads/crm_prod.sql`.

1. **Back up the current local DB first:**
   ```bash
   docker exec commcorp_db mariadb-dump -uroot -p<root-pw> --routines --triggers \
     --single-transaction commcorp_tb > ~/Downloads/commcorp_tb_local_backup_$(date +%Y%m%d).sql
   ```
2. **Recreate the database and import** as root, with a **non-strict `sql_mode`**.
   Navicat writes a value for the generated column `opportunity_product.total`, which
   MariaDB rejects in strict mode (`ERROR 1906 ... generated column 'total'`). In
   non-strict mode the value is ignored and recomputed, so the data stays correct.
   ```bash
   docker exec commcorp_db mariadb -uroot -p<root-pw> -e \
     "DROP DATABASE commcorp_tb; CREATE DATABASE commcorp_tb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
   (echo "SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION';"; cat ~/Downloads/crm_prod.sql) \
     | docker exec -i commcorp_db mariadb -uroot -p<root-pw> commcorp_tb
   ```
   Recreating the DB also drops local-only tables and local migration rows, so the
   local DB matches production exactly.
3. **Verify** the counts against the dump (37 tables, 2 procedures, 19 triggers as of
   2026-09):
   ```sql
   SELECT COUNT(*) FROM information_schema.tables   WHERE table_schema='commcorp_tb';
   SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema='commcorp_tb';
   SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema='commcorp_tb';
   ```
   A quick per-table check: compare `grep -c '^INSERT INTO \`<table>\`'` in the dump
   with `SELECT COUNT(*)` in the DB.
4. Run `migrate/up` afterwards if the repo has migrations production hasn't had yet.

Navicat only exports the object types that were ticked. If production has something
the dump lacks (views, extra procedures), re-export with them selected.

## Migrations

```bash
docker exec commcorp_php php /var/www/html/yii migrate/up --interactive=0
docker exec commcorp_php php /var/www/html/yii migrate/down 1 --interactive=0
```

Naming follows Yii's `mYYMMDD_HHMMSS_<description>.php`. Existing ones that matter:

| Migration | What |
|---|---|
| `m260911_160307_add_parent_product_id_to_product_table` | `product.parent_product_id` hierarchy |
| `m260914_151934_add_customer_type_revenue_model_to_product_table` | `customer_type`, `revenue_model` |
| `m260914_170500_split_multi_segment_products` | one product row per customer-type/revenue-model combination |
| `m260928_090000_create_sales_role` | `sales` basic role |
| `m260928_100000_create_product_pricing_role` | `productPricing` basic role |
| `m260928_120000_fix_duplicate_sales_order_items` | stops quotation approval duplicating SO items |
| `m260928_130000_account_brd_fields` | address types, customer segment, assigned sales, account documents |
| `m260928_140000_add_customer_segment_to_lead` | customer segment on lead, copied by `sp_convert_lead_to_customer` |
| `m261002_090000_sales_order_form` | Sales Order form PDF: SO periods, RFS, PKS, installation/billing address + contact, confirmed_at/by; product service type + bandwidth; SO PDF permission |
| `m260930_090000_business_line` | business line on product and quotation, SPH number per line (`001/SPH/SLS-{LINE}/EXT/…`), opportunity closes when all quotations are decided |
| `m260929_110000_quotation_signer` | signer name / job title on the SPH (template default, per quotation) |
| `m260929_100000_quotation_sph` | SPH numbering + PDF: `quotation_layout`, quotation terms/contract fields, product package info, user job title, permissions |
| `m260929_090000_convert_copies_lead_description` | convert copies the lead's description into the account and the opportunity (lead description is required) |
| `m260928_180000_quotation_from_opportunity_permission` | permission for the Create Quotation button (Sales + Sales Manager) |
| `m260928_150000_convert_lead_adds_account_addresses` | convert also adds the lead's address as Billing, Shipping and Office account addresses |

After a migration runs locally, prepare the production hand-off described in
[deployment-workflow.md](deployment-workflow.md).

## Testing things that touch triggers

Many flows have DB side effects (see [database.md](database.md)). To try one without
touching `commcorp_tb`, copy it into a scratch database, run the statement there,
and drop it:

```bash
docker exec commcorp_db sh -c '
  mariadb -uroot -p<root-pw> -e "DROP DATABASE IF EXISTS trgtest; CREATE DATABASE trgtest;" &&
  mariadb-dump -uroot -p<root-pw> --routines --triggers commcorp_tb \
    | sed -E "s/DEFINER=\`[^\`]+\`@\`[^\`]+\`//g" | mariadb -uroot -p<root-pw> trgtest &&
  mariadb -uroot -p<root-pw> trgtest -e "UPDATE quotation SET status=\"Approved\", updated_by=6 WHERE id=4;
    SELECT * FROM sales_order WHERE quotation_id=4;" ;
  mariadb -uroot -p<root-pw> -e "DROP DATABASE trgtest;"'
```

## Useful local facts

- Local user accounts mirror production: `root` (root), `gatot` (superAdmin),
  `mary` (viewApplication), plus test users with the `sales` / `productPricing` roles.
- `backend/web/index.php` is tracked with `YII_DEBUG = true` / `YII_ENV = 'dev'`.
  See [known-issues.md](known-issues.md) for why that matters on production.
- Gii is at http://localhost:8086/gii.
- App logs: `backend/runtime/logs/app.log`.
