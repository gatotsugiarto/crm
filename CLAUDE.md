# commcorp CRM

Yii2 Advanced Template CRM app (PHP), used by MNC Group / Nextsys. Modules: `auth`,
`master`, `productprice`, `sales`, `logdata` (audit log — despite the folder name,
it's not payroll-specific; `LoggableBehavior` uses it across every module).

## Product classification

Before classifying or creating `product` records (Category / UOM / Type / Parent
Product), read `docs/product-classification.md` — it has the decision rules for each
field and the full "Revenue MKM" BRD product hierarchy already mapped out (Vision+ TV,
Multicast, NextGO, NextSys, IPTV IOH, ICT Business, Data Center External and all their
sub-products). It also documents a known gap: Customer Type and Revenue Model from the
BRD aren't real fields yet, just represented via the `parent_product_id` tree as a
workaround.

## Config files with real credentials are gitignored

`common/config/main-local.php`, `backend/config/main-local.php`, etc. are gitignored
(per-directory `.gitignore` files, standard Yii2 advanced template setup) and never
tracked — that's expected, not a bug to fix.

## Local dev DB

Docker Compose in `docker/` — MariaDB exposed on host port `3309`, app user
`commcorp_2026_tb` / password in `docker/docker-compose.yml`. Database `commcorp_tb`
currently holds a full imported snapshot of the production database (including its
stored procedures and triggers), not just seed/test data — treat it accordingly when
reading table contents.
