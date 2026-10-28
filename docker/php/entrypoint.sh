#!/bin/sh
set -eu

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/database bootstrap/cache

# SQLite: pastikan file database sudah ada sebelum aplikasi connect.
# Tanpa ini, queue/scheduler/web gagal start dengan
# "Database file at path ... does not exist".
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    db_path="${DB_DATABASE:-storage/database/database.sqlite}"
    case "$db_path" in
        /*)
            mkdir -p "$(dirname "$db_path")"
            [ -f "$db_path" ] || : > "$db_path"
            ;;
    esac
fi

exec "$@"
