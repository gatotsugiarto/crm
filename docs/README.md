# commcorp CRM Documentation

CRM for MNC Playmedia / Nextsys, built on the Yii 2 Advanced Template. It covers the
B2B sales pipeline (lead → account → opportunity → quotation → sales order →
invoice), a product catalogue with pricing, master data, user/role management, and
an audit log.

- **Production:** `crm@fe1-webapps:~/public_html`, database `crm_prod` (MySQL 5.5)
- **Dev server:** `/home/crm-dev/public_html`
- **Local:** Docker, http://localhost:8086, database `commcorp_tb` (MariaDB 10.11)
- **Repo:** https://github.com/gatotsugiarto/crm (branch `main`)

## Module status (2026-09-28)

| Module | Status |
|---|---|
| Product & Pricing | **Complete** |
| Sales CRM | In use; open items in [known-issues.md](known-issues.md) (SO item duplication, pricing lookup) |
| Master, Auth, Log Activity | In use; Master Company / Member screens are broken (missing tables) |

## Start here

| Document | Read it when |
|---|---|
| [architecture.md](architecture.md) | You need the stack, directory layout, modules, or the CRUD/modal/behavior patterns every screen follows |
| [database.md](database.md) | You touch tables, write a migration, or wonder why data changed on its own (all 19 triggers and 2 procedures are listed) |
| [sales-flow.md](sales-flow.md) | You work on anything in the Sales menu, or need to explain the pipeline to a user |
| [rbac.md](rbac.md) | You add a screen, add a role, or debug a 403 |
| [product-classification.md](product-classification.md) | You classify or create `product` records (Category, UOM, Type, Customer Type, Revenue Model, parent hierarchy) |
| [local-development.md](local-development.md) | You set up Docker, refresh the local DB from a production dump, or run migrations |
| [deployment-workflow.md](deployment-workflow.md) | You commit, push, deploy, or need to hand SQL to the user for production |
| [known-issues.md](known-issues.md) | Before fixing a bug (it may already be diagnosed), or when planning work |

## Five things that surprise people

1. **Business logic lives in MySQL triggers and procedures.** Approving a quotation
   creates the Sales Order in a trigger, not in PHP. See [database.md](database.md).
2. **`owner_user_id` references `team.id`**, not `user.id`.
3. **`status_id` is an Active/Non Active flag** on every table, separate from business
   statuses like `quotation.status`.
4. **Claude can't reach production or push to GitHub.** It commits, the user
   pushes and runs the SQL/migrations. See [deployment-workflow.md](deployment-workflow.md).
5. **Only the backend app is used.** `frontend/` is template boilerplate.

## Other files in the repo

- `CLAUDE.md` (repo root): short instructions for Claude Code sessions. It points here.
- `common/modules/productprice/models/CRM.txt`: original design notes (menu plan,
  pricing and bundle concepts).
- `backend/views/site/documentation.php`: the in-app user guide (Documentation menu).
- `scripts/production/*.sql`: SQL handed to the user to run on production, one file
  per change. Gitignored, so they exist only on the laptop.
