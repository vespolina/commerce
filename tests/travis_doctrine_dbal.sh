#!/bin/bash

#composer require jackalope/jackalope-doctrine-dbal:~1.0.0-beta1 --prefer-source


SCRIPT_NAME="${0##*/}"
SCRIPT_DIR="${0%/*}"

# if the script was started from the base directory, then the
# expansion returns a period
if test "$SCRIPT_DIR" == "." ; then
  SCRIPT_DIR="$PWD"
# if the script was not called with an absolute path, then we need to add the
# current working directory to the relative path of the script
elif test "${SCRIPT_DIR:0:1}" != "/" ; then
  SCRIPT_DIR="$PWD/$SCRIPT_DIR"
fi

mysql -e 'create database IF NOT EXISTS v_product_tests;' -u root

../vendor/doctrine/phpcr-odm/bin/phpcrodm jackalope:init:dbal
../vendor/doctrine/phpcr-odm/bin/phpcrodm doctrine:phpcr:register-system-node-types