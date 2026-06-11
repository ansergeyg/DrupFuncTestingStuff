#!/usr/bin/env bash
set -euo pipefail

DRUPALTEST_REPO="${DRUPALTEST_REPO:-https://github.com/ansergeyg/drupaltest.git}"
DRUPALTEST_DIR="${DRUPALTEST_DIR:-/tmp/drupaltest}"
MODULE_DIR="${MODULE_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
MODULE_LINK_NAME="${MODULE_LINK_NAME:-drup_func_testing_stuff}"

if [ ! -d "$DRUPALTEST_DIR/.git" ]; then
  rm -rf "$DRUPALTEST_DIR"
  git clone "$DRUPALTEST_REPO" "$DRUPALTEST_DIR"
fi

cd "$DRUPALTEST_DIR"

composer install --no-interaction --no-scripts
composer require --no-interaction --no-scripts --dev drupal/core-dev:^10.5
composer require --no-interaction --no-scripts drupal/paragraphs:^1.18

mkdir -p web/modules/custom
ln -sfn "$MODULE_DIR" "web/modules/custom/$MODULE_LINK_NAME"

printf 'Drupal test project is ready at %s\n' "$DRUPALTEST_DIR"
printf 'Module is linked at %s/web/modules/custom/%s\n' "$DRUPALTEST_DIR" "$MODULE_LINK_NAME"
