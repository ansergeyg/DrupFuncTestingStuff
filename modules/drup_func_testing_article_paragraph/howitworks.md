# How it works

This fixture adds a simple Paragraphs field to the reusable Article content type.

## Configuration

- `paragraphs.paragraphs_type.text_block.yml` creates a Text block paragraph type.
- `field.storage.paragraph.field_text.yml` and `field.field.paragraph.text_block.field_text.yml` add text content to that paragraph type.
- Paragraph form and view display config make the text field editable and renderable.
- `field.storage.node.field_paragraphs.yml` and `field.field.node.article.field_paragraphs.yml` add an Article reference-revisions field limited to Text block paragraphs.
- The install hook attaches the field to the existing Article form and view displays.

## Test coverage

The functional test creates a Text block paragraph, references it from an Article node, visits the node, and checks that the paragraph text renders.
