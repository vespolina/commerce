#!/usr/bin/env sh

echo 'composer install'
export COMPOSER_PROCESS_TIMEOUT=999999999; composer install

echo 'phpunit'
phpunit