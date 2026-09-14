# Deployment Workflow

How code and database changes get from a Claude Code session into production for
this app.

## Code changes

1. **Claude commits + pushes** to `origin/main` — only after the user has explicitly
   confirmed the change is ready (never push unprompted).
2. **User logs into the production server and pulls.** The server is reached via SSH
   as `crm@fe1-webapps`, app root at `~/public_html` (same repo layout as local:
   `backend/`, `common/`, `console/`, `frontend/`, `vendor/`, etc.).
3. That's it for pure code changes (models, views, controllers, migrations *files*) —
   no further action needed on Claude's side once pushed.

## Database schema changes (migrations)

Claude **cannot** reach the production database directly — confirmed: Claude Code's
auto-mode classifier blocks direct reads/writes to it even when given real
credentials and even when the network path is actually reachable (the classifier
denial was labeled `[Production Reads]`). Don't spend time re-attempting a direct
`mysql`/`mysqldump` connection to production from a Claude session — it will be
denied by design, not a fixable permissions issue. Treat "adjust directly to prod" as
effectively never available; plan on the SQL-file path below by default.

So the actual flow for any migration:

1. **Write and test the migration against local first.** Local `commcorp_tb` holds a
   full imported snapshot of production (schema + data + stored procedures +
   triggers — see the "Local dev DB" note below), so a migration that runs clean
   locally is a real signal it'll run clean on production too.
   ```bash
   docker exec commcorp_php php /var/www/html/yii migrate/up --interactive=0
   ```
   Verify the resulting column/table with a quick `DESCRIBE`/`SHOW CREATE TABLE`
   against the local DB before calling it done.

2. **Give the user the equivalent raw SQL** for production, as both:
   - **Option A (recommended):** tell them to run `php yii migrate/up` on the
     production server after they've pulled — this is the cleanest path since it
     also records the migration in the `migration` table, keeping history in sync.
   - **Option B:** raw `ALTER TABLE` / `CREATE TABLE` SQL to run manually (e.g. via
     Navicat) if they don't have console/SSH access to run migrations. If they go
     this route, also give them the matching
     `INSERT INTO migration (version, apply_time) VALUES (...)` so
     `php yii migrate/up` doesn't try to re-apply it later and fail on
     "already exists".

3. Never claim a migration "has been applied to production" — only "applied and
   verified locally; here's what to run on production."

## Local dev DB

Docker Compose in `docker/` — MariaDB exposed on host port `3309`, app user
`commcorp_2026_tb` / password in `docker/docker-compose.yml`. Database `commcorp_tb`
currently holds a full imported snapshot of the production database (including its
stored procedures and triggers), not just seed/test data — treat it accordingly when
reading table contents, and prefer testing schema changes against it before writing
the production SQL.
