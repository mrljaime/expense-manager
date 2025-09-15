#!/bin/sh
set -e

# Let's check if exists some installation
if [ ! -f $UNIQUE_INSTALLATION ]; then
  rm -Rf tmp/
  composer create-project symfony/skeleton:"$SYMFONY_VERSION" tmp --prefer-dist --no-progress --no-interaction --no-install
  cd tmp
  cp -Rp . ..
  cd -
  rm -Rf tmp/

  composer config --json extra.symfony.docker 'false'

  # Install basic elements
  composer install --prefer-dist --no-progress --no-interaction
  composer require logger --no-interaction
  composer require symfony/orm-pack --no-interaction
  composer require --dev symfony/maker-bundle --no-interaction
  echo "Project created with version $SYMFONY_VERSION" > INSTALLATION
fi

if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

exec docker-php-entrypoint "$@"
