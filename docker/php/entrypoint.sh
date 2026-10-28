#!/bin/sh
set -eu

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/database bootstrap/cache

exec "$@"
