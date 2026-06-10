#!/usr/bin/env bash
set -euo pipefail

DRUPALTEST_REPO="${DRUPALTEST_REPO:-https://github.com/ansergeyg/drupaltest.git}"
DRUPALTEST_DIR="${DRUPALTEST_DIR:-/tmp/drupaltest}"
MODULE_DIR="${MODULE_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
MODULE_LINK_NAME="${MODULE_LINK_NAME:-drup_func_testing_stuff}"
DRUPAL_CORE_DEV_CONSTRAINT="${DRUPAL_CORE_DEV_CONSTRAINT:-auto}"
DRUPAL_PARAGRAPHS_CONSTRAINT="${DRUPAL_PARAGRAPHS_CONSTRAINT:-^1.18}"
SKIP_DRUPALTEST_COMPOSER_REQUIRE="${SKIP_DRUPALTEST_COMPOSER_REQUIRE:-0}"
CHECK_DRUPAL_PACKAGES_CONNECTIVITY="${CHECK_DRUPAL_PACKAGES_CONNECTIVITY:-1}"

if [ "$CHECK_DRUPAL_PACKAGES_CONNECTIVITY" = "1" ]; then
  if ! curl -fsSI --max-time 20 https://packages.drupal.org/8/packages.json >/dev/null; then
    cat >&2 <<'MESSAGE'
Unable to reach https://packages.drupal.org/8/packages.json.
Drupal Composer installs require access to packages.drupal.org.
If you are running in a restricted/proxied environment, run this script in CI/local
with Drupal package access or set CHECK_DRUPAL_PACKAGES_CONNECTIVITY=0 to skip this
preflight check and let Composer report the detailed failure.
MESSAGE
    exit 2
  fi
fi

if [ ! -d "$DRUPALTEST_DIR/.git" ]; then
  rm -rf "$DRUPALTEST_DIR"
  git clone "$DRUPALTEST_REPO" "$DRUPALTEST_DIR"
fi

cd "$DRUPALTEST_DIR"

composer install --no-interaction --no-scripts

if [ "$SKIP_DRUPALTEST_COMPOSER_REQUIRE" != "1" ]; then
  if [ "$DRUPAL_CORE_DEV_CONSTRAINT" = "auto" ]; then
    DRUPAL_CORE_DEV_CONSTRAINT="$(composer show drupal/core --format=json | php -r '$data = json_decode(stream_get_contents(STDIN), true); $version = $data["versions"][0] ?? ""; if (!preg_match("/^(\\d+)/", $version, $matches)) { fwrite(STDERR, "Unable to detect installed drupal/core major version from: $version\\n"); exit(1); } echo "^" . $matches[1];')"
    printf 'Detected drupal/core-dev constraint: %s\n' "$DRUPAL_CORE_DEV_CONSTRAINT"
  fi

  composer require --no-interaction --no-scripts --with-all-dependencies --dev "drupal/core-dev:$DRUPAL_CORE_DEV_CONSTRAINT"
  composer require --no-interaction --no-scripts --with-all-dependencies "drupal/paragraphs:$DRUPAL_PARAGRAPHS_CONSTRAINT"
fi

mkdir -p web/modules/custom
ln -sfn "$MODULE_DIR" "web/modules/custom/$MODULE_LINK_NAME"

printf 'Drupal test project is ready at %s\n' "$DRUPALTEST_DIR"
printf 'Module is linked at %s/web/modules/custom/%s\n' "$DRUPALTEST_DIR" "$MODULE_LINK_NAME"
