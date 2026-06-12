# How it works

This fixture installs a minimal `article` node type with a translatable Body field.

## Configuration

- `node.type.article.yml` creates the Article content type.
- `field.field.node.article.body.yml` attaches Drupal core's reusable Body storage to Article.
- `core.entity_form_display.node.article.default.yml` puts Title and Body on the edit form.
- `core.entity_view_display.node.article.default.yml` renders Body on the node page.
- `core.entity_view_display.node.article.teaser.yml` provides a basic teaser display.

## Test coverage

The functional test installs this module, creates an article, visits it, and checks that the title and body render.
