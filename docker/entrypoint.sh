#!/usr/bin/env bash

composer install
npm install
npm run build
bin/console doctrine:migrations:migrate
bin/console doctrine:migrations:migrate --env=test

exec "$@"
