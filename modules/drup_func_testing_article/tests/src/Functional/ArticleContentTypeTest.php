<?php

namespace Drupal\Tests\drup_func_testing_article\Functional;

use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Article fixture content type.
 *
 * @group drup_func_testing_stuff
 */
final class ArticleContentTypeTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drup_func_testing_article',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that Article nodes can be created and rendered.
   */
  public function testArticleContentTypeRenders(): void {
    $this->assertInstanceOf(NodeType::class, NodeType::load('article'));

    $node = Node::create([
      'type' => 'article',
      'title' => 'Fixture article title',
      'body' => [
        'value' => 'Fixture article body text.',
        'format' => 'plain_text',
      ],
      'status' => TRUE,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Fixture article title');
    $this->assertSession()->pageTextContains('Fixture article body text.');
  }

}
