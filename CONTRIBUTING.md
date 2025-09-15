# Contributing Guide

This project is designed to be developed and tested inside Docker. Please read and follow the guidelines below to avoid environment issues.

## Where to run commands?
- Run ALL PHP, Composer, Symfony (bin/console), and PHPUnit commands inside the Docker `php` container.
- Do NOT run these on your host machine; host PHP versions/extensions may not match and will cause errors.

Quick examples:
- Start services: `docker compose up -d`
- Open a shell in the PHP container: `docker compose exec php bash`
- Composer: `docker compose exec php composer install`
- Symfony Console: `docker compose exec php php bin/console cache:clear`
- Migrations: `docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction`
- PHPUnit (test env): `docker compose exec -e APP_ENV=test php ./vendor/bin/phpunit -v`

## PhpStorm configuration (recommended)
To ensure tools run inside the container when triggered from PhpStorm:
1. Settings/Preferences > PHP > CLI Interpreter:
   - Add interpreter via Docker Compose, select service `php`, working dir `/opt/app`.
   - Ensure path mappings point project root -> `/opt/app`.
2. PHP > Composer:
   - Use the same CLI interpreter; enable "Use project composer.json".
3. PHP > Test Frameworks:
   - Add PHPUnit by Remote Interpreter using the same Docker interpreter.
   - Autoloader: `vendor/autoload.php`.
   - Configuration file: `phpunit.dist.xml`.

## About Junie (this assistant)
- Junie does NOT execute anything on your local host/PhpStorm. There is no access to your machine.
- Any file listings or outputs mentioned by Junie are based on the repository contents available in the session, not by running commands on your computer.

## Foundry/Factories in tests
- The project includes Zenstruck Foundry for factory-style testing.
- Base test case: `tests/Helper/FoundryTestCase.php`.
- Example smoke test: `tests/FactorySmokeTest.php`.

Thanks for contributing!
