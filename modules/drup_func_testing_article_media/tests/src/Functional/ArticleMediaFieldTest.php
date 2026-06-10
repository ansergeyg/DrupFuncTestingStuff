<?php

namespace Drupal\Tests\drup_func_testing_article_media\Functional;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Article media fixture.
 *
 * @group drup_func_testing_stuff
 */
final class ArticleMediaFieldTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drup_func_testing_article_media',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that Article nodes can reference image media.
   */
  public function testArticleMediaFieldRenders(): void {
    $this->assertNotNull($this->container->get('entity_field.manager')->getFieldDefinitions('node', 'article')['field_media'] ?? NULL);

    $image_uri = 'public://fixture-image.png';
    file_put_contents($image_uri, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='));

    $file = File::create([
      'uri' => $image_uri,
      'filename' => 'fixture-image.png',
      'status' => FILE_STATUS_PERMANENT,
    ]);
    $file->save();

    $media = Media::create([
      'bundle' => 'image',
      'name' => 'Fixture image media',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => 'Fixture image alt text',
      ],
      'status' => TRUE,
    ]);
    $media->save();

    $node = Node::create([
      'type' => 'article',
      'title' => 'Article with media',
      'field_media' => [
        'target_id' => $media->id(),
      ],
      'status' => TRUE,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Article with media');
    $this->assertSession()->elementAttributeContains('css', 'img', 'alt', 'Fixture image alt text');
  }

}
