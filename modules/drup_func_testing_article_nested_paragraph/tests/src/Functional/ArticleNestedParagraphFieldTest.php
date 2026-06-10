<?php

namespace Drupal\Tests\drup_func_testing_article_nested_paragraph\Functional;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Article nested paragraph fixture.
 *
 * @group drup_func_testing_stuff
 */
final class ArticleNestedParagraphFieldTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drup_func_testing_article_nested_paragraph',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that Article nodes render nested accordion paragraphs.
   */
  public function testArticleNestedParagraphFieldRenders(): void {
    $item = Paragraph::create([
      'type' => 'accordion_item',
      'field_item_title' => 'Nested item title',
      'field_item_body' => [
        'value' => 'Nested item body text.',
        'format' => 'plain_text',
      ],
    ]);
    $item->save();

    $accordion = Paragraph::create([
      'type' => 'accordion',
      'field_accordion_title' => 'Parent accordion title',
      'field_accordion_items' => [
        [
          'target_id' => $item->id(),
          'target_revision_id' => $item->getRevisionId(),
        ],
      ],
    ]);
    $accordion->save();

    $node = Node::create([
      'type' => 'article',
      'title' => 'Article with nested paragraph',
      'field_content_blocks' => [
        [
          'target_id' => $accordion->id(),
          'target_revision_id' => $accordion->getRevisionId(),
        ],
      ],
      'status' => TRUE,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Article with nested paragraph');
    $this->assertSession()->pageTextContains('Parent accordion title');
    $this->assertSession()->pageTextContains('Nested item title');
    $this->assertSession()->pageTextContains('Nested item body text.');
  }

}
