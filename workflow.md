# GitHub Actions workflow

This project includes `.github/workflows/drupal-functional-tests.yml` to run the fixture module functional tests on pull requests.

## What the workflow does

1. Starts a GitHub-hosted Ubuntu runner.
2. Starts a MySQL 8 service matching the `ansergeyg/drupaltest` local database hostname.
3. Installs PHP 8.3, Composer, and Drupal-required PHP extensions.
4. Clones `https://github.com/ansergeyg/drupaltest.git` into `/tmp/drupaltest`.
5. Runs Composer install for the clean Drupal harness.
6. Adds test-only dependencies that this fixture project needs:
   - `drupal/core-dev` at the exact installed `drupal/core` version.
   - `drupal/paragraphs:^1.18`.
   - `drupal/entity_reference_revisions:^1`.
7. Allows Composer plugins needed by Drupal development tooling:
   - `phpstan/extension-installer`.
   - `dealerdirect/phpcodesniffer-composer-installer`.
8. Scaffolds Drupal files and installs the clean Drupal site with `vendor/bin/robo drup`.
9. Symlinks this repository into `web/modules/custom/drup_func_testing_stuff`.
10. Starts PHP's built-in web server.
11. Runs the `BrowserTestBase` tests in `modules/*/tests/src/Functional`.

## How to enable it on GitHub

1. Open this repository on GitHub.
2. Click **Settings**.
3. Click **Actions** → **General**.
4. Under **Actions permissions**, allow GitHub Actions for this repository.
5. Under **Workflow permissions**, **Read repository contents and packages permissions** is enough.
6. Open the **Actions** tab.
7. If GitHub asks to enable workflows, confirm that you want to enable them.
8. Open a pull request. The workflow named **Drupal functional tests** should start automatically.

You can also run it manually from **Actions** → **Drupal functional tests** → **Run workflow**.

## Important environment variables

| Variable | Default | Purpose |
| --- | --- | --- |
| `DRUPALTEST_REPO` | `https://github.com/ansergeyg/drupaltest.git` | Drupal harness repository. |
| `DRUPALTEST_DIR` | `/tmp/drupaltest` | Temporary Drupal harness directory. |
| `MODULE_LINK_NAME` | `drup_func_testing_stuff` | Symlink name under `web/modules/custom`. |
| `DRUPAL_PARAGRAPHS_CONSTRAINT` | `^1.18` | Paragraphs package constraint. |
| `DRUPAL_ENTITY_REFERENCE_REVISIONS_CONSTRAINT` | `^1` | Entity Reference Revisions package constraint. |
| `SIMPLETEST_BASE_URL` | `http://127.0.0.1:8080` | BrowserTestBase base URL. |
| `SIMPLETEST_DB` | `mysql://root:@127.0.0.1/drupal` | BrowserTestBase database URL. |

## Composer dependency notes

The CI prepare script uses `--with-all-dependencies` when requiring missing test dependencies. This lets Composer adjust locked transitive packages, which is necessary when adding `drupal/core-dev` to an existing Drupal project.

The script requires `drupal/core-dev` at the exact installed `drupal/core` version instead of a loose major constraint. That keeps Drupal core and core-dev synchronized with the version selected by `ansergeyg/drupaltest`.
