<?php

namespace Drupal\Tests\drup_func_testing_article_paragraph\Functional;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Article paragraph fixture.
 *
 * @group drup_func_testing_stuff
 */
final class ArticleParagraphFieldTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drup_func_testing_article_paragraph',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that Article nodes can reference simple paragraphs.
   */
  public function testArticleParagraphFieldRenders(): void {
    $paragraph = Paragraph::create([
      'type' => 'text_block',
      'field_text' => [
        'value' => 'Simple paragraph fixture text.',
        'format' => 'plain_text',
      ],
    ]);
    $paragraph->save();

    $node = Node::create([
      'type' => 'article',
      'title' => 'Article with paragraph',
      'field_paragraphs' => [
        [
          'target_id' => $paragraph->id(),
          'target_revision_id' => $paragraph->getRevisionId(),
        ],
      ],
      'status' => TRUE,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Article with paragraph');
    $this->assertSession()->pageTextContains('Simple paragraph fixture text.');
  }

}
