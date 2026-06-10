<?php

namespace Drupal\Tests\drup_func_testing_article_revision\Functional;

use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Article revision fixture.
 *
 * @group drup_func_testing_stuff
 */
final class ArticleRevisionTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drup_func_testing_article_revision',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that Article nodes create revisions by default.
   */
  public function testArticleCreatesDefaultRevisions(): void {
    $type = NodeType::load('article');
    $this->assertNotNull($type);
    $this->assertTrue($type->shouldCreateNewRevision());

    $node = Node::create([
      'type' => 'article',
      'title' => 'Revision fixture title',
      'status' => TRUE,
    ]);
    $node->save();

    $node->setTitle('Revision fixture title updated');
    $node->save();

    $revision_ids = $this->container->get('entity_type.manager')->getStorage('node')->revisionIds($node);
    $this->assertGreaterThanOrEqual(2, count($revision_ids));

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Revision fixture title updated');
  }

}
