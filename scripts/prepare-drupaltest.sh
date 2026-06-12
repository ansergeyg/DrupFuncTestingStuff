#!/usr/bin/env bash
set -euo pipefail

DRUPALTEST_REPO="${DRUPALTEST_REPO:-https://github.com/ansergeyg/drupaltest.git}"
DRUPALTEST_DIR="${DRUPALTEST_DIR:-/tmp/drupaltest}"
MODULE_DIR="${MODULE_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
MODULE_LINK_NAME="${MODULE_LINK_NAME:-drup_func_testing_stuff}"
DRUPAL_PARAGRAPHS_CONSTRAINT="${DRUPAL_PARAGRAPHS_CONSTRAINT:-^1.18}"
DRUPAL_ENTITY_REFERENCE_REVISIONS_CONSTRAINT="${DRUPAL_ENTITY_REFERENCE_REVISIONS_CONSTRAINT:-^1}"

if [ ! -d "$DRUPALTEST_DIR/.git" ]; then
  rm -rf "$DRUPALTEST_DIR"
  git clone "$DRUPALTEST_REPO" "$DRUPALTEST_DIR"
fi

cd "$DRUPALTEST_DIR"

# Composer 2 blocks new plugins in non-interactive environments unless they are
# explicitly allow-listed. Drupal core-dev can bring these plugins in through
# code quality tooling, so allow the known plugins before install/require.
composer config --no-interaction --no-plugins allow-plugins.phpstan/extension-installer true
composer config --no-interaction --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true

composer validate --no-check-lock --strict
composer install --prefer-dist --no-progress --no-interaction --no-scripts

DRUPAL_CORE_VERSION="$(composer show drupal/core --format=json | php -r '$data = json_decode(stream_get_contents(STDIN), true); echo $data["versions"][0] ?? "";')"
if [ -z "$DRUPAL_CORE_VERSION" ]; then
  echo 'Unable to detect installed drupal/core version.' >&2
  exit 1
fi

composer require \
  --prefer-dist \
  --no-progress \
  --no-interaction \
  --no-scripts \
  --with-all-dependencies \
  --dev \
  "drupal/core-dev:$DRUPAL_CORE_VERSION" \
  "drupal/paragraphs:$DRUPAL_PARAGRAPHS_CONSTRAINT" \
  "drupal/entity_reference_revisions:$DRUPAL_ENTITY_REFERENCE_REVISIONS_CONSTRAINT"

composer drupal:scaffold
composer run-script post-update-cmd

mkdir -p web/modules/custom
ln -sfn "$MODULE_DIR" "web/modules/custom/$MODULE_LINK_NAME"

PATH="$PWD/vendor/bin:$PATH" vendor/bin/robo drup

printf 'Drupal test project is ready at %s\n' "$DRUPALTEST_DIR"
printf 'Module is linked at %s/web/modules/custom/%s\n' "$DRUPALTEST_DIR" "$MODULE_LINK_NAME"
printf 'Installed drupal/core version: %s\n' "$DRUPAL_CORE_VERSION"
