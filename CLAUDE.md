# commcorp CRM

Yii2 Advanced Template CRM app (PHP), used by MNC Group / Nextsys. Modules: `auth`,
`master`, `productprice`, `sales`, `logdata` (audit log — despite the folder name,
it's not payroll-specific; `LoggableBehavior` uses it across every module).

## Product classification

Before classifying or creating `product` records (Category / UOM / Type / Customer
Type / Revenue Model / Parent Product), read `docs/product-classification.md` — it
has the decision rules for each field and the full "Revenue MKM" BRD product
hierarchy already mapped out (Vision+ TV, Multicast, NextGO, NextSys, IPTV IOH, ICT
Business, Data Center External and all their sub-products).

## Deploying changes (code + database)

Read `docs/deployment-workflow.md` before pushing anything or writing a migration —
it covers the commit/push/pull flow, and importantly: Claude cannot reach the
production database directly (blocked by Claude Code's own auto-mode classifier, not
a fixable permissions issue) — migrations get tested against local, then handed to
the user as SQL to run on production themselves.

## Config files with real credentials are gitignored

`common/config/main-local.php`, `backend/config/main-local.php`, etc. are gitignored
(per-directory `.gitignore` files, standard Yii2 advanced template setup) and never
tracked — that's expected, not a bug to fix.
