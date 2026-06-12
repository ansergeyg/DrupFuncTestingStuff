<?php

namespace Drupal\Tests\drup_func_testing_article_translation\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\Node;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Article translation fixture.
 *
 * @group drup_func_testing_stuff
 */
final class ArticleTranslationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drup_func_testing_article_translation',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that Article nodes can be translated.
   */
  public function testArticleTranslationRenders(): void {
    $french = ConfigurableLanguage::load('fr');
    $this->assertNotNull($french);
    $this->assertTrue($this->container->get('content_translation.manager')->isEnabled('node', 'article'));

    $node = Node::create([
      'type' => 'article',
      'title' => 'English fixture title',
      'body' => [
        'value' => 'English fixture body.',
        'format' => 'plain_text',
      ],
      'langcode' => 'en',
      'status' => TRUE,
    ]);
    $node->save();

    $node->addTranslation('fr', [
      'title' => 'Titre de test français',
      'body' => [
        'value' => 'Corps de test français.',
        'format' => 'plain_text',
      ],
      'status' => TRUE,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl('canonical', ['language' => $french]));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Titre de test français');
    $this->assertSession()->pageTextContains('Corps de test français.');
  }

}
