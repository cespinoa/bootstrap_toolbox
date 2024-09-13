<?php

/**
 * @file
 * Contains \Drupal\bootstrap_toolbox\Plugin\views\display_extender\BootstrapToolbox.
 */

namespace Drupal\bootstrap_toolbox\Plugin\views\display_extender;

use Drupal\views\Plugin\views\display_extender\DisplayExtenderPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Config\ConfigFactoryInterface;



/**
 * Bootstrap Toolbox display extender plugin.
 *
 * @ingroup views_display_extender_plugins
 *
 * @ViewsDisplayExtender(
 *   id = "bootstrap_toolbox",
 *   title = @Translation("bootstrap toolbox display extender"),
 *   help = @Translation("Settings to add metatag in document head for this view."),
 *   no_ui = FALSE
 * )
 */
class BootstrapToolbox extends DisplayExtenderPluginBase {

  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected $utilityService;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a BootstrapToolbox object.
   *
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *   The utility service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(UtilityServiceInterface $utilityService,
  ConfigFactoryInterface $config_factory) {
    $this->utilityService = $utilityService;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service'),
      $container->get('config.factory')
    );
  }
  /**
   * Provide the key options for this plugin.
   */
  public function defineOptionsAlter(&$options) {
    $options['bootstrap_toolbox'] =  array(
      'contains' => array(
        'hide_title' => array('default' => FALSE),
        'hide_sidebars' => array('default' => FALSE),
        'hide_breadcrumb' => array('default' => FALSE),
        'edge_to_edge' => array('default' => FALSE),
        'custom_theme' => array('default' => ''),
      )
    );
  }

  /**
   * Provide the default summary for options and category in the views UI.
   */
  public function optionsSummary(&$categories, &$options) {
    $categories['bootstrap_toolbox'] = array(
      'title' => t('Bootstrap Toolbox'),
      'column' => 'second',
    );
    $settings = $this->options['bootstrap_toolbox'];
    $msg[] = $settings['hide_title'] ? $this->t('Hide title') : '';
    $msg[] = $settings['hide_sidebars'] ? $this->t('Hide sidebars') : '';
    $msg[] = $settings['hide_breadcrumb'] ? $this->t('Hide breadcrumb') : '';
    $msg[] = $settings['edge_to_edge'] ? $this->t('Display edge to edge') : '';
    $msg[] = $settings['custom_theme'] ? $this->t('Custom theme is @custom_theme', ['@custom_theme' => $settings['custom_theme']]) : '';
    $msg = $this->utilityService->filterNonEmptyValues($msg);
    if($msg){
      $msg = implode(', ', $msg);
    }
    else {
      $msg = $this->t('Configure');
    }
    
    
    $bootstrap_toolbox = $this->hasData() ? $this->getBootstrapToolboxValues() : FALSE;
    $options['bootstrap_toolbox'] = array(
      'category' => 'bootstrap_toolbox',
      'title' => t('Bootstrap Toolbox'),
      'value' => $msg,
    );
  }

  /**
   * Provide a form to edit options for this plugin.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {

    if ($form_state->get('section') == 'bootstrap_toolbox') {
      $form['#title'] .= t('Bootstrap Toolbox settings for this display');
      $bootstrap_toolbox = $this->getBootstrapToolboxValues();
      

      $form['bootstrap_toolbox']['#type'] = 'container';
      $form['bootstrap_toolbox']['#tree'] = TRUE;

      $form['bootstrap_toolbox']['hide_title'] = array(
        '#title' => $this->t('Hide title'),
        '#type' => 'checkbox',
        '#default_value' => $bootstrap_toolbox['hide_title'],
      );

      $form['bootstrap_toolbox']['hide_sidebars'] = array(
        '#title' => $this->t('Hide sidebars'),
        '#type' => 'checkbox',
        '#default_value' => $bootstrap_toolbox['hide_sidebars'],
      );

      $form['bootstrap_toolbox']['hide_breadcrumb'] = array(
        '#title' => $this->t('Hide breadcrumb'),
        '#type' => 'checkbox',
        '#default_value' => $bootstrap_toolbox['hide_breadcrumb'],
      );

      $form['bootstrap_toolbox']['edge_to_edge'] = array(
        '#title' => $this->t('Display edge to edge'),
        '#type' => 'checkbox',
        '#default_value' => $bootstrap_toolbox['edge_to_edge'],
      );

      $form['bootstrap_toolbox']['custom_theme'] = array(
        '#title' => $this->t('Select theme'),
        '#type' => 'select',
        '#default_value' => $bootstrap_toolbox['custom_theme'],
        '#empty_option' => t('None'),
        '#options' => $this->utilityService->getAllowedThemes(),
      );
      
    }
  }

  /**
   * Validate the options form.
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) { }

  /**
   * Handle any special handling on the validate form.
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    if ($form_state->get('section') == 'bootstrap_toolbox') {
      $bootstrap_toolbox = $form_state->getValue('bootstrap_toolbox');
      $this->options['bootstrap_toolbox'] = $bootstrap_toolbox;

      $viewId = $this->view->id();
      $displayId = $this->view->current_display;

      //~ // Recupera la configuración existente.
      $config = $this->configFactory->getEditable('bootstrap_toolbox.settings');
      $existing_values = $config->get('views_custom_themes') ?: [];

      // Genera la clave única basada en el nombre de la vista y el display.
      $key = "{$viewId}_{$displayId}";

      // Obtiene los valores del formulario.
      //~ $bootstrap_toolbox_settings = $form_state->getValue('bootstrap_toolbox');

      // Almacena los valores en la configuración.
      $existing_values[$key] = $bootstrap_toolbox;
      

      // Guarda la configuración actualizada.
      $config->set('views_custom_themes', $existing_values)->save();
    

      
    }
  }

  /**
   * Set up any variables on the view prior to execution.
   */
  public function preExecute() { }

  /**
   * Inject anything into the query that the display_extender handler needs.
   */
  public function query() { }

  /**
   * Static member function to list which sections are defaultable
   * and what items each section contains.
   */
  public function defaultableSections(&$sections, $section = NULL) { }

  /**
   * Identify whether or not the current display has custom metadata defined.
   */
  public function hasData() {
    $bootstrap_toolbox = $this->getBootstrapToolboxValues();
    return !empty($bootstrap_toolbox['hide_title']);
  }

  /**
   * Get the bootstrap toolbox configuration for this display.
   *
   * @return array
   *   The bootstrap toolbox values.
   */
  public function getBootstrapToolboxValues() {
    if(array_key_exists('bootstrap_toolbox', $this->options)){
      return $this->options['bootstrap_toolbox'];  
    }
    
    return [];
  }
}
