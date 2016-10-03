#!/usr/bin/env sh

vendor/bin/phinx create $1 \
    -c application/configs/phinx.local.yml \
    --template="data/phinx-migrations/phinx_migration.php.dist"
