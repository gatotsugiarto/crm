#!/usr/bin/env bash
#
# Plain mysqldump wrapper. Credentials are never hardcoded here — pass
# them as arguments (or export MYSQL_PWD before running so the password
# doesn't show up in shell history / `ps aux`).
#
# Usage:
#   scripts/dump-db.sh <host> <port> <user> <dbname> [output.sql]

set -e

HOST="$1"
PORT="$2"
USER="$3"
DBNAME="$4"
OUT="${5:-${DBNAME}_$(date +%Y%m%d_%H%M%S).sql}"

mysqldump -h "$HOST" -P "$PORT" -u "$USER" -p "$DBNAME" > "$OUT"

echo "Saved to $OUT"
