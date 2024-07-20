<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox_carousel\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Bootstrap Toolbox Carousel style plugin.
 *
 * @ViewsStyle(
 *   id = "bootstrap_toolbox_carousel_view_style",
 *   title = @Translation("Bootstrap Toolbox Carousel"),
 *   help = @Translation("Display content as a Bootstrap carousel."),
 *   theme = "views_view_bootstrap_toolbox_carousel_view_style",
 *   display_types = {"normal"},
 * )
 */
final class BootstrapToolboxCarouselViewStyle extends StylePluginBase {

  // Default values for options.
  private const DEFAULT_SHOW_INDICATORS = TRUE;
  private const DEFAULT_SHOW_CONTROLS = TRUE;
  private const DEFAULT_INTERVAL = 6000;

  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /**
   * {@inheritdoc}
   */
  protected $usesRowClass = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['show_indicators'] = ['default' => self::DEFAULT_SHOW_INDICATORS];
    $options['show_controls'] = ['default' => self::DEFAULT_SHOW_CONTROLS];
    $options['interval'] = ['default' => self::DEFAULT_INTERVAL];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    $form['show_indicators'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show indicators'),
      '#default_value' => $this->options['show_indicators'],
    ];

    $form['show_controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show controls'),
      '#default_value' => $this->options['show_controls'],
    ];

    $form['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval'),
      '#default_value' => $this->options['interval'],
      '#description' => $this->t('Set the interval in milliseconds between slide transitions.'),
      '#min' => 1000,
      '#step' => 500,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Validate interval.
    $interval = $form_state->getValue(['style_options', 'interval']);
    if (!is_numeric($interval) || $interval <= 0) {
      $form_state->setErrorByName('interval', $this->t('The interval must be a positive number.'));
    }

    // Save the validated values.
    $this->options['show_indicators'] = $form_state->getValue(['style_options', 'show_indicators']);
    $this->options['show_controls'] = $form_state->getValue(['style_options', 'show_controls']);
    $this->options['interval'] = $form_state->getValue(['style_options', 'interval']);
  }

}
