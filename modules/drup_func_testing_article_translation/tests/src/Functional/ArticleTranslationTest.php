<?php

namespace Drupal\Tests\drup_func_testing_article_translation\Functional;

use Drupal\Core\Language\LanguageInterface;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationUrl;
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
    $this->assertNotNull(ConfigurableLanguage::load('fr'));
    $this->assertTrue($this->container->get('content_translation.manager')->isEnabled('node', 'article'));

    $this->config('language.negotiation')
      ->set('url.prefixes.fr', 'fr')
      ->save();
    $this->container->get('language_negotiator')->saveConfiguration(LanguageInterface::TYPE_CONTENT, [
      LanguageNegotiationUrl::METHOD_ID => 0,
    ]);
    $this->container->get('language_manager')->reset();

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

    $this->drupalGet('fr/node/' . $node->id());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Titre de test français');
    $this->assertSession()->pageTextContains('Corps de test français.');
  }

}
