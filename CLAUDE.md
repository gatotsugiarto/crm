# commcorp CRM

Yii2 Advanced Template CRM app (PHP), used by MNC Group / Nextsys. Modules: `auth`,
`master`, `productprice`, `sales`, `logdata` (audit log — despite the folder name,
it's not payroll-specific; `LoggableBehavior` uses it across every module).

All project documentation is in `docs/` — start at `docs/README.md`. Keep new
Markdown docs there too (not in the repo root or a `memory/` folder).

## Read before you act

- **Classifying or creating `product` records** (Category / UOM / Type / Customer
  Type / Revenue Model / Parent Product): `docs/product-classification.md` — decision
  rules plus the full "Revenue MKM" BRD product hierarchy already mapped out.
- **Pushing, deploying, or writing a migration**: `docs/deployment-workflow.md`.
  Claude cannot reach the production database (blocked by Claude Code's auto-mode
  classifier, not a fixable permissions issue) and cannot `git push` — it commits,
  the user pushes and runs migrations/SQL on production.
- **Anything in the Sales menu, or data that changes "by itself"**:
  `docs/database.md` and `docs/sales-flow.md` — much of the logic is MySQL triggers
  and stored procedures.
- **Roles, permissions, 403s**: `docs/rbac.md`.
- **Refreshing the local DB from a production dump**: `docs/local-development.md`
  (import needs a non-strict `sql_mode`).
- **Before fixing a bug**: check `docs/known-issues.md`.

## Config files with real credentials are gitignored

`common/config/main-local.php`, `backend/config/main-local.php`, etc. are gitignored
(per-directory `.gitignore` files, standard Yii2 advanced template setup) and never
tracked — that's expected, not a bug to fix.
