# How it works

This fixture configures the reusable Article content type to create a new revision by default.

## Configuration

Node entities are revisionable in Drupal core. This fixture does not install a separate revision module. Instead, its install hook loads the Article node type and sets `new_revision` to `TRUE`.

## Test coverage

The functional test verifies that Article has default revisions enabled, saves an Article twice, and checks that Drupal stored multiple revision IDs.
