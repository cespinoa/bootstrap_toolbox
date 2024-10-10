<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;


/**
 * Configure Bootstrap Toolbox settings for this site.
 */
final class MainSettingsForm extends ConfigFormBase {

  /**
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   *
   * The utility service.
   */
  protected $utilityService;

  /**
   * {@inheritdoc}
   */
  public function __construct(UtilityServiceInterface $utilityService) {
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service'),
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

    $form['fp_options'] = [
      '#type' => 'details',
      '#title' => $this->t('Front page options'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
    ];

    $form['base_theme_list'] = [
      '#type' => 'details',
      '#title' => $this->t('Base themes'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#open' => TRUE,
      '#group' => 'verticaltabs',
      '#attributes' => [
        'id' => ['base_theme_list'],
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
      '#attributes' => [
        'id' => ['custom_css'],
      ],
    ];

    $form['available_themes'] = [
      '#type' => 'details',
      '#title' => $this->t('Available themes'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'verticaltabs',
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

     
    $form['base_theme_list']['create_new_item'] = [
      '#type' => 'link',
      '#title' => $this->t('+ Add base theme'),
      '#url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.basethemes', ['action' => 'create']),
      '#attributes' => [
        'class' => ['button', 'button--primary'], 
      ],
    ];

    $header = [
      $this->t('Base theme'),
      $this->t('Actions')
    ];
    $rows = [];

    
    $baseThemes = $this->utilityService->getConfigBaseThemes() ?? [];
    foreach( $baseThemes as $themeId => $themeName){
      $dropbutton = [
        '#type' => 'dropbutton',
        '#dropbutton_type' => 'small',
        '#links' => [
          'copy' => [
            'title' => $this->t('Copy'),
            'url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.basethemes', ['action' => 'copy', 'themeId' => $themeId]),
          ],
        ],
      ];
      $rendered = $this->utilityService->renderArray($dropbutton);

      if ($rendered instanceof \Drupal\Core\Render\Markup) {
          $markup = $rendered; 
      } elseif (is_string($rendered)) {
          $markup = $this->utilityService->createMarkup($rendered);
      } else {
          $markup = '';
      }
      $rows[] = [
        'data' => [
          $themeName . '(*)',
          $markup,
        ],
      ];
    }
    

    $customBaseThemes = $this->utilityService->getCustomBasethemes() ?? [];
    foreach($customBaseThemes as $themeId => $themeData){

      $dropbutton = [
        '#type' => 'dropbutton',
        '#dropbutton_type' => 'small',
        '#links' => [
          'edit' => [
            'title' => $this->t('Edit'),
            'url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.basethemes', ['action' => 'edit', 'themeId' => $themeId]),
          ],
          'copy' => [
            'title' => $this->t('Copy'),
            'url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.basethemes', ['action' => 'copy', 'themeId' => $themeId]),
          ],
          'delete' => [
            'title' => $this->t('Delete'),
            'url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.basethemes', ['action' => 'delete','themeId' => $themeId]),
          ],
        ],
      ];
      $rendered = $this->utilityService->renderArray($dropbutton);

      if ($rendered instanceof \Drupal\Core\Render\Markup) {
          $markup = $rendered;
      } elseif (is_string($rendered)) {
          $markup = $this->utilityService->createMarkup($rendered);
      } else {
          $markup = '';
      }
      
      $rows[] = [
        'data' => [
          $themeData['name'],
          $markup,
        ],
      ];
    }
    
    $form['base_theme_list']['list'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No custom base theme availables'),
    ];

    $form['base_theme_list']['list_description'] = [
      '#type' => 'markup',
      '#markup' => $this->t('(*) System base themes can´t be deleted or modified'),
    ];
    

    $form['base_theme_list']['list']['#attached']['library'][] = 'core/drupal.dropbutton'; // Table don´t load library

    $form['base_theme_list']['list']['#attached']['library'][] = 'bootstrap_toolbox/custom';
    
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
      '#options' => $this->utilityService->getThemes(),
      '#default_value' => $config->get('selected_themes') ? $config->get('selected_themes') : '', 
    ];

    $publicPath = $this->utilityService->realPath('public://');

    $fileUrl = $publicPath . '/bootstrap_toolbox/custom.css';

    if (file_exists($fileUrl)) {
      $msg1 = $this->t('You can customize your css by editing directlly the file ');
      $msg2 = '<br/><i>' . $fileUrl . '</i><br/>';
      if ($this->utilityService->checkModule('codemirror_editor')){
        $msg3 = '';
        $msg4 = '';
      }
      else {
        $msg3 = $this->t('It might be a good idea to install');
        $msg4 = ' <a href="https://www.drupal.org/project/codemirror_editor">Code Mirror Editor</a>';
      }
      
      $form['custom_css']['message'] = [
        '#markup' => $msg1 . $msg2 . $msg3. $msg4,
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];
      $form['custom_css']['edit_custom_css'] = [
        '#type' => 'link',
        '#title' => $this->t('Edit custom css'),
        '#url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.edit_custom_css'),
        '#attributes' => [
          'class' => ['button', 'button--primary'], 
        ],
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
        '#markup' => $this->utilityService->renderArray($link),
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





}
