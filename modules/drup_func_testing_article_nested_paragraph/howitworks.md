# How it works

This fixture adds accordion-style nested Paragraphs to the reusable Article content type.

## Configuration

- `paragraphs.paragraphs_type.accordion.yml` creates the parent Accordion paragraph type.
- `paragraphs.paragraphs_type.accordion_item.yml` creates nested Accordion item paragraphs.
- Accordion has a title field and `field_accordion_items`, an entity-reference-revisions field limited to Accordion item paragraphs.
- Accordion item has title and body fields.
- Article receives `field_content_blocks`, an entity-reference-revisions field limited to Accordion paragraphs.
- The install hook attaches `field_content_blocks` to the existing Article form and view displays.

## Test coverage

The functional test creates an Accordion item, nests it inside an Accordion paragraph, references the Accordion from an Article, visits the node, and checks that both parent and nested values render.
