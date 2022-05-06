#!/usr/bin/env bash

composer install
npm install
npm run build
bin/console doctrine:migrations:migrate

exec "$@"

