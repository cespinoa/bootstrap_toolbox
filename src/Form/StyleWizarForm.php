<?php

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Theme\ThemeInitializationInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Implements a custom form for Bootstrap style preview.
 */
class StyleWizarForm extends FormBase {

  /**
   * The theme manager service.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * The theme initialization service.
   *
   * @var \Drupal\Core\Theme\ThemeInitializationInterface
   */
  protected $themeInitialization;

  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected UtilityServiceInterface $utilityService;

  /**
   * Constructs a new YourFormClass.
   *
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager service.
   * @param \Drupal\Core\Theme\ThemeInitializationInterface $theme_initialization
   *   The theme initialization service.
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *  The utility service.
   */
  public function __construct(
    ThemeManagerInterface $theme_manager,
    ThemeInitializationInterface $theme_initialization,
    UtilityServiceInterface $utilityService
  ) {
    $this->themeManager = $theme_manager;
    $this->themeInitialization = $theme_initialization;
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('theme.manager'),
      $container->get('theme.initialization'),
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'style_wizar_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $siteTheme = $this->utilityService->getDefaultTheme();
    $this->themeManager->setActiveTheme($this->themeInitialization->initTheme($siteTheme));

    // Bootstrap class options.
    $textColors = [
      '' => $this->t('Select text color'),
      'text-primary' => $this->t('Primary'),
      'text-secondary' => $this->t('Secondary'),
      'text-success' => $this->t('Success'),
      'text-danger' => $this->t('Danger'),
      'text-warning' => $this->t('Warning'),
      'text-info' => $this->t('Info'),
      'text-light' => $this->t('Light'),
      'text-dark' => $this->t('Dark'),
      'text-body' => $this->t('Body'),
      'text-body-secondary' => $this->t('Body secondary'),
      'text-body-tertiary' => $this->t('Body tertiary'),
      'text-white' => $this->t('White'),
      'text-black' => $this->t('Black'),
      'text-black-50' => $this->t('Black 50'),
    ];

    $backgroundColors = [
      '' => $this->t('Select background color'),
      'bg-primary' => $this->t('Primary'),
      'bg-primary-subtle' => $this->t('Primary subtle'),
      'bg-secondary' => $this->t('Secondary'),
      'bg-secondary-subtle' => $this->t('Secondary subtle'),
      'bg-success' => $this->t('Success'),
      'bg-success-subtle' => $this->t('Success subtle'),
      'bg-danger' => $this->t('Danger'),
      'bg-danger-subtle' => $this->t('Danger subtle'),
      'bg-warning' => $this->t('Warning'),
      'bg-warning-subtle' => $this->t('Warning subtle'),
      'bg-info' => $this->t('Info'),
      'bg-info-subtle' => $this->t('Info subtle'),
      'bg-light' => $this->t('Light'),
      'bg-light-subtle' => $this->t('Light subtle'),
      'bg-dark' => $this->t('Dark'),
      'bg-dark-subtle' => $this->t('Dark subtle'),
      'bg-body' => $this->t('Body'),
      'bg-body-secondary' => $this->t('Body secondary'),
      'bg-body-tertiary' => $this->t('Body tertiary'),
      'bg-black' => $this->t('Black'),
      'bg-white' => $this->t('White'),
      'bg-transparent' => $this->t('Transparent'),
    ];

    $textSizes = [
      '' => $this->t('Select text size'),
      'fs-1' => $this->t('Size 1'),
      'fs-2' => $this->t('Size 2'),
      'fs-3' => $this->t('Size 3'),
      'fs-4' => $this->t('Size 4'),
      'fs-5' => $this->t('Size 5'),
      'fs-6' => $this->t('Size 6'),
    ];

    $padding = [
      '' => $this->t('Select padding'),
      'p-0' => $this->t('None'),
      'p-1' => $this->t('Padding 1'),
      'p-2' => $this->t('Padding 2'),
      'p-3' => $this->t('Padding 3'),
      'p-4' => $this->t('Padding 4'),
      'p-5' => $this->t('Padding 5'),
    ];

    $margin = [
      '' => $this->t('Select margin'),
      'm-0' => $this->t('None'),
      'm-1' => $this->t('Margin 1'),
      'm-2' => $this->t('Margin 2'),
      'm-3' => $this->t('Margin 3'),
      'm-4' => $this->t('Margin 4'),
      'm-5' => $this->t('Margin 5'),
    ];

    $shadow = [
      '' => $this->t('Select shadow'),
      'shadow-sm' => $this->t('Small shadow'),
      'shadow-p' => $this->t('Regular shadow'),
      'shadow-lg' => $this->t('Larger shadow'),
    ];

    $opacity = [
      '' => $this->t('Select opacity'),
      'opacity-0' => $this->t('Opacity 0'),
      'opacity-25' => $this->t('Opacity 25'),
      'opacity-50' => $this->t('Opacity 50'),
      'opacity-75' => $this->t('Opacity 75'),
      'opacity-100' => $this->t('Opacity 100'),
    ];

    $rounded = [
      '' => $this->t('Select rounded'),
      'rounded-0' => $this->t('Rounded 0'),
      'rounded-1' => $this->t('Rounded 1'),
      'rounded-2' => $this->t('Rounded 2'),
      'rounded-3' => $this->t('Rounded 3'),
      'rounded-4' => $this->t('Rounded 4'),
      'rounded-5' => $this->t('Rounded 5'),
    ];

    $weight = [
      '' => $this->t('Select font weight'),
      'fw-bold' => $this->t('Bold'),
      'fw-bolder' => $this->t('Bolder'),
      'fw-semibold' => $this->t('Semibold'),
      'fw-medium' => $this->t('Medium'),
      'fw-normal' => $this->t('Normal'),
      'fw-light' => $this->t('Light'),
      'fw-lighter' => $this->t('Lighter'),
    ];

    $variant = [
      '' => $this->t('Select font variant'),
      'fst-normal' => $this->t('Normal'),
      'fst-italic' => $this->t('Italic'),
    ];

    $lineHeight = [
      '' => $this->t('Select line height'),
      'lh-1' => $this->t('Minimum'),
      'lh-sm' => $this->t('Medium'),
      'lh-base' => $this->t('Base'),
      'lh-lg' => $this->t('Large'),
    ];

    $alignment = [
      '' => $this->t('Select alignment'),
      'text-start' => $this->t('Left'),
      'text-center' => $this->t('Center'),
      'text-end' => $this->t('Right'),
    ];

    $classesStr = $this->getRequest()->query->get('classes', '');
    $classesStr = is_string($classesStr) ? $classesStr : '';
    $classes = explode(' ', $classesStr);

    $arrays = ['textColors', 'backgroundColors', 'textSizes', 'padding', 'margin',
      'shadow', 'opacity', 'rounded', 'weight', 'variant', 'lineHeight', 'alignment',
    ];

    $settings = [];
    $settings['another_classes'] = NULL;
    foreach ($arrays as $array) {
      $settings[$array] = NULL;
      $intersect = array_intersect($classes, array_keys($$array));

      if ($intersect) {
        $intersect = reset($intersect);
        $key = array_search($intersect, $classes);
        unset($classes[$key]);
        $classes = array_values($classes);
        $settings[$array] = $intersect;
      }
    }
    if ($classes) {
      $settings['another_classes'] = implode(' ', $classes);
    }
    
    // Add form elements.
    $styleName = $this->getRequest()->query->get('style_name', NULL);
    if ($styleName) {
      $form['header'] = [
        '#markup' => '<h3 class="text-center mb-3">' .
        $this->t('Editing @style_name style', ['@style_name' => $styleName]) .
        '</h3>',
      ];
    }

    $form['custom_text_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Text Color'),
      '#options' => $textColors,
      '#default_value' => $settings['textColors'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_background_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Background Color'),
      '#options' => $backgroundColors,
      '#default_value' => $settings['backgroundColors'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_text_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Text Size'),
      '#options' => $textSizes,
      '#default_value' => $settings['textSizes'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_padding'] = [
      '#type' => 'select',
      '#title' => $this->t('Padding'),
      '#options' => $padding,
      '#default_value' => $settings['padding'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_margin'] = [
      '#type' => 'select',
      '#title' => $this->t('Margin'),
      '#options' => $margin,
      '#default_value' => $settings['margin'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_shadow'] = [
      '#type' => 'select',
      '#title' => $this->t('Shadow'),
      '#options' => $shadow,
      '#default_value' => $settings['shadow'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_opacity'] = [
      '#type' => 'select',
      '#title' => $this->t('Opacity'),
      '#options' => $opacity,
      '#default_value' => $settings['opacity'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_rounded'] = [
      '#type' => 'select',
      '#title' => $this->t('Rounded'),
      '#options' => $rounded,
      '#default_value' => $settings['rounded'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_weight'] = [
      '#type' => 'select',
      '#title' => $this->t('Font weight'),
      '#options' => $weight,
      '#default_value' => $settings['weight'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_variant'] = [
      '#type' => 'select',
      '#title' => $this->t('Font variant'),
      '#options' => $variant,
      '#default_value' => $settings['variant'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_line_height'] = [
      '#type' => 'select',
      '#title' => $this->t('Line height'),
      '#options' => $lineHeight,
      '#default_value' => $settings['lineHeight'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_alignment'] = [
      '#type' => 'select',
      '#title' => $this->t('Text alignment'),
      '#options' => $alignment,
      '#default_value' => $settings['alignment'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    $form['custom_another_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Anhoter classes'),
      '#default_value' => $settings['another_classes'],
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'preview-wrapper',
      ],
    ];

    foreach ($form as $key => $control) {
      $form[$key]['#prefix'] = '<div class="form-inline-item">';
      $form[$key]['#suffix'] = '</div>';
    }

    $form['preview'] = [
      '#type' => 'markup',
      '#markup' => '<div class="preview-text ' . $classesStr . '">' . $this->t('Classes:') . $classesStr . '</div>',
      '#prefix' => '<div id="preview-wrapper">',
      '#suffix' => '</div>',
      '#attributes' => [
        'class' => ['mt-5 mb-5'],
      ],
    ];

    $originalStyle = $this->getRequest()->query->get('style', NULL);
    if ($this->getRequest()->query->get('style')) {

      $form['actions']['save_style'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save style'),
        '#button_type' => 'primary',
        '#attributes' => [
          'class' => ['mt-5 mb-5'],
        ],
        '#submit' => ['::saveStyle'],
      ];
    }

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add style'),
      '#button_type' => 'primary',
      '#submit' => ['::submitForm'],
      '#attributes' => [
        'class' => ['mt-5 mb-5'],
      ],
    ];

    $form['#attached']['library'][] = 'bootstrap_toolbox/inline_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function updatePreview(array &$form, FormStateInterface $form_state) {
    $classString = $this->processClasses($form_state->getValues());
    $form['preview']['#markup'] = '<div class="preview-text ' .
      $classString . '">' . $this->t('Classes: ') . $classString . '</div>';
    return $form['preview'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $classes = $this->processClasses($form_state->getValues());
    $form_state->setRedirect('entity.bootstrap_toolbox_style.add_form', ['classes' => $classes]);
  }

  /**
   * {@inheritdoc}
   */
  public function saveStyle(array &$form, FormStateInterface $form_state) {
    $entityId = $this->getRequest()->query->get('style', NULL);
    $classes = $this->processClasses($form_state->getValues());
    $form_state->setRedirect('entity.bootstrap_toolbox_style.edit_form', [
      'bootstrap_toolbox_style' => $entityId,
      'classes' => $classes,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function processClasses($values) {
    $classes = [];
    foreach ($values as $key => $class) {
      if (substr($key, 0, 6) == 'custom') {
        $classes[] = $class;
      }
    }
    $classes = array_filter($classes);
    $classString = implode(' ', $classes);
    return $classString;
  }

}
