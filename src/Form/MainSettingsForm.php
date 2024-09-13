<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Extension\ExtensionDiscovery;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Bootstrap Toolbox settings for this site.
 */
final class MainSettingsForm extends ConfigFormBase {

  /**
   * @var Drupal\Core\Theme\ThemeHandlerInterface
   *
   * ThemeHandlerInterface service
   * */
  protected $themeHandler;

  /**
   * @var Drupal\bootstrap_toolbox\UtilityServiceInterface
   *
   * The utility service
   * */
  protected $utilityService;

  /**
   * {@inheritdoc}
   */
  public function __construct(UtilityServiceInterface $utilityService,
  ThemeHandlerInterface $themeHandler ) {
    $this->utilityService = $utilityService;
    $this->themeHandler = $themeHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service'),
      $container->get('theme_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bootstrap_toolbox_main_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bootstrap_toolbox.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config('bootstrap_toolbox.settings');

    $form['verticaltabs'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['library'] = [
      '#type' => 'details',
      '#title' => $this->t('Bootstrap library'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#group' => 'verticaltabs',
    ];

    $form['fp_options'] = [
      '#type' => 'details',
      '#title' => $this->t('Front page options'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
    ];

    $form['edge_to_edge_control'] = [
      '#type' => 'details',
      '#title' => $this->t('Behaviors selectors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
      '#attributes' => [
        'class' => ['horizontal-form'],
      ],
    ];

    $form['edit_mode_options'] = [
      '#type' => 'details',
      '#title' => $this->t('Mode edit options'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
    ];

    $form['custom_css'] = [
      '#type' => 'details',
      '#title' => $this->t('Custom css'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
    ];

    $form['available_themes'] = [
      '#type' => 'details',
      '#title' => $this->t('Available themes'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
    ];

    $form['library']['selected_library'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a library'),
      '#default_value' => $config->get('selected_library'),
      '#options' => $this->getExtensions(),
      '#description' => $this->t('Select which library to use for the Bootstrap Toolbox.'),
    ];

    $form['fp_options']['front_page_options'] = [
      '#type' => 'checkboxes',
      '#title' => t('Front page options'),
      '#options' => [
        'edge_to_edge' => t('Edge to Edge'),
        'hide_sidebars' => t('Hide Sidebars'),
        'hide_title' => t('Hide title'),
        'hide_breadcrumb' => t('Hide breadcrumb'),
      ],
      '#default_value' => $config->get('front_page_options') ? $config->get('front_page_options') : [],
    ];

    $form['fp_options']['custom_theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Custom theme'),
      '#options' => $this->utilityService->getAllowedThemes() ,
      '#empty_option' => $this->t('None'),
      '#default_value' => $config->get('custom_theme') ? $config->get('custom_theme') : NULL,
    ];

    $form['edge_to_edge_control']['selected_theme'] = [
      '#type' => 'select',
      '#title' => t('Theme or base theme'),
      '#options' => $this->utilityService->getKnownThemes(),
      '#default_value' => $config->get('selected_theme') ? $config->get('selected_theme') : [],
    ];

    $form['edge_to_edge_control']['sidebars_variables'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sidebars classes'),
      '#default_value' => $config->get('sidebars_variables') ? $config->get('sidebars_variables') : 'sidebar_first sidebar_second',
      '#description' => $this->t('The names of sidebars variables.<br/> You can find out their names by looking in the theme\'s info file.<br/>Write their names separating them with a space'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['main_area_selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Main area selector'),
      '#default_value' => $config->get('main_area_selector') ? $config->get('main_area_selector') : '#main',
      '#description' => $this->t('The main area selector.<br/>If it is an id you must write it preceded by the # sign, if it is a class you must start it with a period. For example: #main or .main-area)<br/>You can see its value by exploring the html of the page'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['main_area_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fluid main area class'),
      '#default_value' => $config->get('main_area_class') ? $config->get('main_area_class') : 'container-fluid',
      '#description' => $this->t('The class corresponding to the fluid container, which occupies the entire width of the screen.<br/>Normally it is container-fluid, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['central_panel_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Main area in edition mode'),
      '#default_value' => $config->get('central_panel_class') ? $config->get('central_panel_class') : 'container',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['edit_mode_fields_area'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field area selector in edition mode'),
      '#default_value' => $config->get('edit_mode_fields_area') ? $config->get('edit_mode_fields_area') : '',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['edit_mode_advanced_area'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Advanced area selector in edition mode'),
      '#default_value' => $config->get('edit_mode_advanced_area') ? $config->get('edit_mode_advanced_area') : '',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['edit_mode_fields_area_remove_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class to remove in main area in edition mode'),
      '#default_value' => $config->get('edit_mode_fields_area_remove_class') ? $config->get('edit_mode_fields_area_remove_class') : '',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edge_to_edge_control']['edit_mode_advanced_area_remove_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class to remove in advanced area in edition mode'),
      '#default_value' => $config->get('edit_mode_advanced_area_remove_class') ? $config->get('edit_mode_advanced_area_remove_class') : '',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="selected_theme"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['edit_mode_options']['edit_mode_hide_sidebars'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide sidebars while edit node'),
      '#default_value' => $config->get('edit_mode_hide_sidebars') ? $config->get('edit_mode_hide_sidebars') : 'false',
    ];

    $form['edit_mode_options']['edit_mode_edge_to_edge'] = [
      '#type' => 'radios',
      '#title' => t('Edge to edge'),
      '#options' => [
        'none' => t('Leave it as in the node'),
        'edge_to_edge' => t('Force edge-to-edge'),
        'central_panel' => t('Force central panel'),
      ],
      '#default_value' => $config->get('edit_mode_edge_to_edge') ? $config->get('edit_mode_edge_to_edge') : 'none',
    ];

    $form['edit_mode_options']['change_areas_width'] = [
      '#type' => 'checkbox',
      '#title' => t('Change areas with'),
      '#default_value' => $config->get('change_areas_width') ? $config->get('change_areas_width') : 'none',
    ];

    $form['edit_mode_options']['edit_mode_fields_area_add_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class to add in main area in edition mode'),
      '#default_value' => $config->get('edit_mode_fields_area_add_class') ? $config->get('edit_mode_fields_area_add_class') : '',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="change_areas_width"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['edit_mode_options']['edit_mode_advanced_area_add_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class to add in advanced area in edition mode'),
      '#default_value' => $config->get('edit_mode_advanced_area_add_class') ? $config->get('edit_mode_advanced_area_add_class') : '',
      '#description' => $this->t('The class corresponding to the main area in edit mode.<br/>Normally it is container, but you can check the Bootstrap documentation.'),
      '#states' => [
        'visible' => [
          ':input[name="change_areas_width"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['available_themes']['selected_themes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Available themes'),
      '#options' => $this->getThemes(),
      '#default_value' => $config->get('selected_themes') ? $config->get('selected_themes') : '', 
    ];

    $publicPath = \Drupal::service('file_system')->realpath('public://');
    $fileUrl = $publicPath . '/bootstrap_toolbox/custom.css';

    if (file_exists($fileUrl)) {
      $msg = $this->t('You can customize your css by editing the file ');
      $form['custom_css']['message'] = [
        '#markup' => $msg . '<br/><i>' . $fileUrl . '</i>',
      ];
    }
    else {
      $msgFirst = $this->t('Bootstrap Toolbox can create a CSS file for you that you can customize your site with.');
      $msgSecond = $this->t('This will allow you to make all the changes you need without affecting the CSS of the theme or the Bootstrap Toolbox tools.');
      $form['custom_css']['message'] = [
        '#markup' => '<p>' . $msgFirst . '<br/>' . $msgSecond . '</p>',
      ];
      $url = Url::fromUri('internal:/create-bootstrap-toolbox');
      $link = Link::fromTextAndUrl($this->t('Create file'), $url)->toRenderable();
      $link['#attributes']['class'] = ['btn', 'btn-primary', 'button', 'button--primary'];

      // Añadir el enlace al formulario.
      $form['custom_css']['create_toolbox_link'] = [
        '#type' => 'markup',
        '#markup' => \Drupal::service('renderer')->render($link),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $settings = $form_state->getValues();
    unset($settings['submit']);
    unset($settings['form_build_id']);
    unset($settings['form_token']);
    unset($settings['form_id']);
    unset($settings['op']);
    foreach($settings as $setting=>$value){
      $this->config('bootstrap_toolbox.settings')
        ->set($setting,$value)
        ->save();
    }
    parent::submitForm($form, $form_state);
  }

  /**
   * Get installed modules and themes with Bootstrap libraries.
   *
   * @return array
   */
  protected function getExtensions():array {
    // Get all extensions available in the system.
    $extensionDiscovery = new ExtensionDiscovery(\Drupal::root());
    $extensions = $extensionDiscovery->scan('module') + $extensionDiscovery->scan('theme');

    $libraryOptions = [];
    $libraryOptions['default'] = $this->t('Default');
    foreach ($extensions as $extension => $extensionInfo) {
      $libraries = \Drupal::service('library.discovery')->getLibrariesByExtension($extension);
      foreach ($libraries as $libName => $lib) {
        if ($this->isBootstrapLibrary($lib)) {
          $libraryOptions["$extension/$libName"] = "$extension/$libName";
        }
      }
    }
    return $libraryOptions;
  }

  /**
   * Get all installed themes.
   *
   * @return array
   */
  protected function getThemes():array {
    $extensionDiscovery = new ExtensionDiscovery(\Drupal::root());
    $extensions = $extensionDiscovery->scan('theme');
    $themes = [];
    foreach ($extensions as $extension => $extensionInfo) {
      $themes[$extension] = $this->themeHandler->getName($extension);
    }
    return $themes;
  }

  /**
   * Check if the library contains bootstrap related files.
   *
   * @param array $library
   *   The library definition array.
   *
   * @return bool
   *   TRUE if the library contains bootstrap related files, FALSE otherwise.
   */
  protected function isBootstrapLibrary(array $library) {

    foreach (['css', 'js'] as $type) {
      if (!empty($library[$type])) {
        foreach ($library[$type] as $files) {
          foreach ($files as $file => $attributes) {
            if (is_string($attributes) && strpos($attributes, 'bootstrap') !== FALSE) {
              $attributes = explode('/', $attributes)[1];
              if (strpos($attributes, 'bootstrap') !== FALSE) {
                return TRUE;
              }
            }
          }
        }
      }
    }
    return FALSE;
  }

}
