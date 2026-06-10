#!/usr/bin/env bash
set -euo pipefail

DRUPALTEST_DIR="${DRUPALTEST_DIR:-/tmp/drupaltest}"
MODULE_LINK_NAME="${MODULE_LINK_NAME:-drup_func_testing_stuff}"
TEST_TARGET="${1:-web/modules/custom/$MODULE_LINK_NAME/modules}"
SIMPLETEST_BASE_URL="${SIMPLETEST_BASE_URL:-http://127.0.0.1:8888}"
SIMPLETEST_DB="${SIMPLETEST_DB:-sqlite://localhost/sites/default/files/.ht.sqlite}"
PHPUNIT_CONFIG="${PHPUNIT_CONFIG:-web/core/phpunit.xml.dist}"
SERVER_HOST="${SERVER_HOST:-127.0.0.1}"
SERVER_PORT="${SERVER_PORT:-8888}"
SERVER_LOG="${SERVER_LOG:-/tmp/drupaltest-php-server.log}"

if [ ! -d "$DRUPALTEST_DIR" ]; then
  echo "Missing Drupal test harness directory: $DRUPALTEST_DIR" >&2
  echo "Run scripts/prepare-drupaltest.sh first." >&2
  exit 1
fi

cd "$DRUPALTEST_DIR"

if [ ! -f "$PHPUNIT_CONFIG" ]; then
  echo "Missing PHPUnit config: $DRUPALTEST_DIR/$PHPUNIT_CONFIG" >&2
  echo "Run scripts/prepare-drupaltest.sh first." >&2
  exit 1
fi

mkdir -p web/sites/default/files

export SIMPLETEST_BASE_URL
export SIMPLETEST_DB

php -S "$SERVER_HOST:$SERVER_PORT" -t web web/.ht.router.php >"$SERVER_LOG" 2>&1 &
SERVER_PID="$!"

cleanup() {
  kill "$SERVER_PID" >/dev/null 2>&1 || true
}
trap cleanup EXIT

for attempt in {1..30}; do
  if php -r "exit(@file_get_contents('$SIMPLETEST_BASE_URL') === false ? 1 : 0);"; then
    break
  fi
  sleep 1
  if [ "$attempt" -eq 30 ]; then
    echo "Timed out waiting for $SIMPLETEST_BASE_URL. Server log follows:" >&2
    cat "$SERVER_LOG" >&2 || true
    exit 1
  fi
done

vendor/bin/phpunit -c "$PHPUNIT_CONFIG" "$TEST_TARGET"
