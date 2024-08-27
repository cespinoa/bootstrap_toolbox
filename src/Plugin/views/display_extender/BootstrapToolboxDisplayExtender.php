<?php

namespace Drupal\bootstrap_toolbox\Plugin\views\display_extender;

use Drupal\views\Plugin\views\display_extender\DisplayExtenderPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Views Display Extender for Bootstrap Toolbox settings.
 *
 * @ingroup views_display_extender_plugins
 *
 * @ViewsDisplayExtender(
 *   id = "bootstrap_toolbox_display_extender",
 *   title = @Translation("Bootstrap Toolbox Settings"),
 *   help = @Translation("Additional Bootstrap Toolbox settings.")
 * )
 */
class BootstrapToolboxDisplayExtender extends DisplayExtenderPluginBase {

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $options = parent::defineOptions();
    $options['bootstrap_toolbox_settings'] = [
      'contains' => [
        'edge_to_edge' => ['default' => FALSE],
        'hide_title' => ['default' => FALSE],
        'hide_sidebars' => ['default' => FALSE],
        'hide_breadcrumb' => ['default' => FALSE],
      ],
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    if ($form_state->get('section') == 'bootstrap_toolbox_settings'){
      
      $form['edge_to_edge'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Display edge to edge'),
        '#default_value' => $this->options['edge_to_edge'],
      ];

      $form['hide_title'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Hide page title'),
        '#default_value' => $this->options['hide_title'],
      ];

      $form['hide_sidebars'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Hide sidebars'),
        '#default_value' => $this->options['hide_sidebars'],
      ];

      $form['hide_breadcrumb'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Hide breadcrumb'),
        '#default_value' => $this->options['hide_breadcrumb'],
      ];
      
    }

  }


  /**
   * Handle any special handling on the validate form.
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    if ($form_state->get('section') == 'bootstrap_toolbox_settings') {
      $this->options['edge_to_edge'] = $form_state->getValue('edge_to_edge');
      $this->options['hide_title'] = $form_state->getValue('hide_title');
      $this->options['hide_sidebars'] = $form_state->getValue('hide_sidebars');
      $this->options['hide_breadcrumb'] = $form_state->getValue('hide_breadcrumb');
    }
  }

  /**
   * Provide the default summary for options in the views UI.
   *
   * This output is returned as an array.
   */
  public function optionsSummary(&$categories, &$options) {

    $categories['bootstrap_toolbox_settings'] = [
      'title' => $this->t('Bootstrap Toolbox'),
      'column' => 'second',
    ];
    $options['bootstrap_toolbox_settings'] = [
      'category' => 'bootstrap_toolbox_settings',
      'title' => $this->t('Bootstrap Toolbox settings'),
      'value' => (empty($this->options['bootstrap_toolbox_settings'])) ? $this->t('Add settings') : $this->t('Edit Settings'),
      'value' => $this->t('Settings'),
    ];

  }

}
