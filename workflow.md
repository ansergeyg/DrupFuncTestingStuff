# GitHub Actions workflow setup

This project includes a GitHub Actions workflow at `.github/workflows/drupal-functional-tests.yml`.

The workflow runs the fixture module functional tests in a GitHub-hosted Ubuntu runner. It checks out this repository, sets up PHP and Composer, prepares a disposable Drupal project from `ansergeyg/drupaltest`, symlinks this module into `web/modules/custom/drup_func_testing_stuff`, starts PHP's built-in web server, and runs Drupal `BrowserTestBase` tests with SQLite.

## What you get after merging this project

After the workflow file is merged into your repository's default branch, GitHub can automatically run Drupal functional tests on:

- Pull requests.
- Pushes to `main` or `master`.
- Manual runs from the GitHub website.

## Step-by-step GitHub website configuration

### 1. Open the repository on GitHub

1. Go to `https://github.com/<your-org-or-user>/<your-repository>`.
2. Make sure the branch selector shows the branch where this project was merged, usually `main` or `master`.

### 2. Confirm Actions are enabled

1. Click the **Settings** tab near the top of the repository page.
2. In the left sidebar, click **Actions**.
3. Click **General**.
4. Under **Actions permissions**, choose one of these options:
   - **Allow all actions and reusable workflows**, or
   - **Allow <your organization> actions and reusable workflows**, plus allow the external actions used by this project.
5. This workflow uses these external actions:
   - `actions/checkout@v4`
   - `shivammathur/setup-php@v2`
   - `actions/cache@v4`
   - `actions/upload-artifact@v4`
6. Click **Save** if GitHub shows a save button.

### 3. Confirm workflow permissions

1. Stay on **Settings** → **Actions** → **General**.
2. Scroll to **Workflow permissions**.
3. Select **Read repository contents and packages permissions**.
4. You do not need write permissions for this test workflow.
5. Click **Save** if GitHub shows a save button.

### 4. Confirm pull request workflows can run

If this repository is under an organization, organization settings may restrict workflow runs from forks.

1. Go to the organization page on GitHub.
2. Click **Settings**.
3. In the left sidebar, click **Actions** → **General**.
4. Review **Fork pull request workflows**.
5. Choose the policy your team wants. For public repositories, GitHub may require approval before first-time contributors can run workflows.

### 5. Open the Actions tab

1. Go back to the repository page.
2. Click the **Actions** tab.
3. In the left workflow list, find **Drupal functional tests**.
4. If GitHub asks whether you want to enable workflows for this repository, click **I understand my workflows, go ahead and enable them**.

### 6. Run the workflow manually

1. In the **Actions** tab, click **Drupal functional tests** in the left sidebar.
2. Click **Run workflow**.
3. Choose the branch to test.
4. Leave `test_target` as the default to run all fixture tests:

   ```text
   web/modules/custom/drup_func_testing_stuff/modules
   ```

5. Click the green **Run workflow** button.
6. Open the new workflow run and watch these steps:
   - **Check out fixture module repository**
   - **Set up PHP**
   - **Prepare Drupal test harness**
   - **Run Drupal fixture functional tests**

### 7. Run only one fixture test manually

When manually running the workflow, you can set `test_target` to one fixture test directory, for example:

```text
web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article/tests/src/Functional
```

Other useful targets:

```text
web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article_media/tests/src/Functional
web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article_paragraph/tests/src/Functional
web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article_nested_paragraph/tests/src/Functional
web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article_translation/tests/src/Functional
web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article_revision/tests/src/Functional
```

### 8. Read failed test logs

1. Open the failed workflow run.
2. Click the failed job, usually **BrowserTestBase fixtures on SQLite**.
3. Expand the failed step.
4. If the PHP built-in server failed or a functional test failed after the server started, check the uploaded artifact named `drupaltest-php-server-log`.
5. Download the artifact from the workflow run summary if GitHub uploaded it.

## Required network access

The GitHub runner must be able to access these public services:

- `github.com` to check out this repository and clone `ansergeyg/drupaltest`.
- `packages.drupal.org` for Drupal Composer package metadata.
- Packagist/Composer package hosts for PHP dependencies.

If your organization restricts outbound network access for Actions runners, allow these hosts or run the workflow on a self-hosted runner that has access.

## Useful workflow environment variables

The workflow uses the same variables as the local harness scripts:

| Variable | Default in workflow | Purpose |
| --- | --- | --- |
| `DRUPALTEST_DIR` | `/tmp/drupaltest` | Where the disposable Drupal harness is cloned. |
| `MODULE_LINK_NAME` | `drup_func_testing_stuff` | Symlink name under `web/modules/custom`. |
| `SIMPLETEST_BASE_URL` | `http://127.0.0.1:8888` | Base URL used by Drupal functional tests. |
| `SIMPLETEST_DB` | `sqlite://localhost/sites/default/files/.ht.sqlite` | SQLite database URL used by `BrowserTestBase`. |
| `SERVER_LOG` | `/tmp/drupaltest-php-server.log` | PHP built-in server log uploaded on failure. |
| `DRUPAL_CORE_DEV_CONSTRAINT` | `auto` | `scripts/prepare-drupaltest.sh` detects the installed Drupal core major version and requires the matching `drupal/core-dev` major version. Override this only when you need a specific constraint. |
| `DRUPAL_PARAGRAPHS_CONSTRAINT` | `^1.18` | Paragraphs dependency constraint used by the disposable harness. |
| `ALLOW_PHPSTAN_EXTENSION_INSTALLER` | `1` | Allows the `phpstan/extension-installer` Composer plugin before install/require operations. Set to `0` only if your harness already handles Composer plugin allow-listing. |

## Composer dependency resolution notes

The prepare script uses Composer's `--with-all-dependencies` option when adding runtime test dependencies. This is important when the `drupaltest` harness has a `composer.lock` file, because adding `drupal/core-dev` can require Symfony package upgrades or downgrades that Composer would otherwise block during a partial update.

By default, `DRUPAL_CORE_DEV_CONSTRAINT` is `auto`. The script reads the installed `drupal/core` major version after `composer install` and then requires the matching `drupal/core-dev` major constraint, such as `^10` or `^11`. This avoids forcing Drupal 10 dev dependencies into a Drupal 11 harness.

The prepare script also allows the `phpstan/extension-installer` Composer plugin by default. Drupal core development dependencies can install PHPStan extensions, and Composer blocks unknown plugins unless they are explicitly listed in `config.allow-plugins`. The script runs `composer config --no-plugins allow-plugins.phpstan/extension-installer true` before Composer install/require operations so the workflow does not stop for interactive plugin approval.

## Optional future improvements

After `ansergeyg/drupaltest` includes `drupal/core-dev`, `drupal/paragraphs`, and a lock file, you can speed up the workflow by setting this environment variable in the workflow:

```yaml
SKIP_DRUPALTEST_COMPOSER_REQUIRE: '1'
```

That tells `scripts/prepare-drupaltest.sh` to skip runtime `composer require` commands and rely on the Drupal harness repository's own dependencies.
