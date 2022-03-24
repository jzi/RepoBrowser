After cloning:

```
composer install
npm install
npm build
php bin/console doctrine:migrations:migrate
symfony server:start
```

To import an organization's repository, run `bin/console app:repo:import <organization> <provider>`. Only Github provider is supported.
