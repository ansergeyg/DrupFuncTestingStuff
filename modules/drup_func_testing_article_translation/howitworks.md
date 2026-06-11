# How it works

This fixture makes the reusable Article content type translatable.

## Configuration

- `language.entity.fr.yml` adds French as a second content language.
- `language.content_settings.node.article.yml` marks Article content as language-alterable and enables content translation.
- The install hook enables content translation for the Article bundle and makes the Body field translatable.

## Test coverage

The functional test creates an English Article, adds a French translation, and checks that the translated title and body render on the French node route.
