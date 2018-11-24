<?php

namespace Drupal\simple_lexer_parser\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'simple_lexer_parser_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "simple_lexer_parser_formatter",
 *   label = @Translation("Simple lexer parser formatter"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *     "text",
 *     "text_long",
 *   }
 * )
 */
class SimpleLexerParserFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'simple_lexer_parser',
        '#type' => 'container',
        '#attributes' => [
          'class' => ['simple-lexer-parser-container'],
        ],
        '#attached' => [
          'library' => [
            'simple_lexer_parser/animate_expression',
          ],
        ],
        '#result' => $this->viewValue($item),
        '#expression' => $item->value,
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $Calc = \Drupal::service('simple_lexer_parser.calculator');
    try {
      $postfix = $Calc->lexer($item->value);
      $result = $Calc->evaluate($postfix);
    }
    catch (\Exception $e) {
      drupal_set_message(t($e->getMessage()), 'error');
      $result = $e->getMessage();
    }
    return nl2br(Html::escape($result));
  }
}
