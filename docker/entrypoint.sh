#!/usr/bin/env bash

composer install

bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate

bin/console doctrine:database:create --if-not-exists --env=test
bin/console doctrine:migrations:migrate --env=test

npm install
npm run build

exec "$@"
