# Drupal boilerplate functional testing

This repository contains copy/paste-friendly Drupal fixture submodules for functional tests. Each submodule installs a focused set of content type, field, translation, revision, media, or Paragraphs configuration and includes a `BrowserTestBase` test that proves the fixture works.

## Modules

| Module | Purpose |
| --- | --- |
| `drup_func_testing_article` | Provides a configured `article` content type with a Body field. |
| `drup_func_testing_article_media` | Adds an image media type and an Article media reference field. |
| `drup_func_testing_article_paragraph` | Adds a simple Text block paragraph type and Article paragraph field. |
| `drup_func_testing_article_nested_paragraph` | Adds accordion-style nested paragraphs to Article. |
| `drup_func_testing_article_translation` | Enables Language and Content Translation for Article and adds French. |
| `drup_func_testing_article_revision` | Enables default revision creation for Article nodes. |

The advanced modules depend on `drup_func_testing_article` instead of duplicating the same `node.type.article` config. This keeps the modules installable together and avoids duplicate Drupal config ownership.

## Copy/paste usage

Copy this repository folder into a Drupal installation as:

```text
web/modules/custom/drup_func_testing_stuff
```

Then enable the fixture module needed for your test. For example:

```bash
drush en drup_func_testing_article_media -y
```

Drupal will also enable dependencies declared in the fixture module `.info.yml` files.

Paragraph fixtures require the contrib `paragraphs` and `entity_reference_revisions` modules to be available in the Drupal project.

## Test harness using `ansergeyg/drupaltest`

The scripts in `scripts/` use [`ansergeyg/drupaltest`](https://github.com/ansergeyg/drupaltest) as a disposable Drupal test harness. The harness is intentionally rebuildable and should be treated as a cache, not committed project state.

Prepare the Drupal test project. This installs Composer dependencies, adds the fixture-only test dependencies, scaffolds Drupal, symlinks this repository into the harness, and installs the clean Drupal site:

```bash
scripts/prepare-drupaltest.sh
```

Run all fixture functional tests:

```bash
scripts/run-functional-tests.sh
```

Run one fixture test directory:

```bash
scripts/run-functional-tests.sh web/modules/custom/drup_func_testing_stuff/modules/drup_func_testing_article/tests/src/Functional
```

### Useful environment variables

| Variable | Default | Description |
| --- | --- | --- |
| `DRUPALTEST_REPO` | `https://github.com/ansergeyg/drupaltest.git` | Drupal harness repository. |
| `DRUPALTEST_DIR` | `/tmp/drupaltest` | Local clone/cache path for the Drupal harness. |
| `MODULE_DIR` | Current repository root | Module package to symlink into the harness. |
| `MODULE_LINK_NAME` | `drup_func_testing_stuff` | Symlink name under `web/modules/custom`. |
| `DRUPAL_PARAGRAPHS_CONSTRAINT` | `^1.18` | Paragraphs package constraint installed for Paragraph fixtures. |
| `DRUPAL_ENTITY_REFERENCE_REVISIONS_CONSTRAINT` | `^1` | Entity Reference Revisions package constraint installed for Paragraph fixtures. |
| `SIMPLETEST_BASE_URL` | `http://127.0.0.1:8888` | Base URL used by Drupal functional tests. |
| `SIMPLETEST_DB` | `sqlite://localhost/sites/default/files/.ht.sqlite` | SQLite database URL used by BrowserTestBase. |

## GitHub Actions

This repository includes `.github/workflows/drupal-functional-tests.yml`, which runs on pull requests and uses `ansergeyg/drupaltest` as a clean Drupal harness. The workflow runs `scripts/prepare-drupaltest.sh`, starts PHP's built-in web server, and runs the `BrowserTestBase` fixture tests against MySQL.

See `workflow.md` for setup details and the exact CI flow.
