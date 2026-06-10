# How it works

This fixture adds image media support to the reusable Article content type.

## Configuration

- `media.type.image.yml` creates an Image media type.
- `field.storage.media.field_media_image.yml` and `field.field.media.image.field_media_image.yml` add the source image field.
- `core.entity_form_display.media.image.default.yml` and `core.entity_view_display.media.image.default.yml` configure media editing and rendering.
- `field.storage.node.field_media.yml` and `field.field.node.article.field_media.yml` add an Article media reference field.
- The install hook attaches `field_media` to the existing Article form and view displays to avoid duplicate display config ownership.

## Test coverage

The functional test creates an image file, media entity, and article referencing the media, then confirms the media output renders on the article page.
