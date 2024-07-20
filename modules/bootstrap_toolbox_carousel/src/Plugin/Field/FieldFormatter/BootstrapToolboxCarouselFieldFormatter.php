<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox_carousel\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

/**
 * Plugin implementation of the 'bootstrap_toolbox_carousel_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "bootstrap_toolbox_carousel_field_formatter",
 *   label = @Translation("Bootstrap Toolbox Carousel"),
 *   field_types = {"entity_reference"},
 * )
 */
final class BootstrapToolboxCarouselFieldFormatter extends FormatterBase {

  
  // Define constants for default settings
  private const SHOW_INDICATORS_DEFAULT = TRUE;
  private const SHOW_CONTROLS_DEFAULT = TRUE;
  private const SHOW_ALT_TEXT_DEFAULT = TRUE;
  private const SHOW_FULL_SCREEN_DEFAULT = FALSE;
  private const INTERVAL_DEFAULT = '5000';
  private const IMAGE_STYLE_DEFAULT = 'thumbnail';
  private const VIEW_MODE_DEFAULT = 'default';
  private const USE_IMAGE_ONLY_DEFAULT = FALSE;
  
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * Constructs a BootstrapToolboxCarouselFieldFormatter object.
   *
   * @param string $plugin_id
   *   The plugin ID for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display repository.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, EntityDisplayRepositoryInterface $entity_display_repository) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityDisplayRepository = $entity_display_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'show_indicators' => self::SHOW_INDICATORS_DEFAULT,
      'show_controls' => self::SHOW_CONTROLS_DEFAULT,
      'show_alt_text' => self::SHOW_ALT_TEXT_DEFAULT,
      'show_full_screen' => self::SHOW_FULL_SCREEN_DEFAULT,
      'interval' => self::INTERVAL_DEFAULT,
      'image_style' => self::IMAGE_STYLE_DEFAULT,
      'view_mode' => self::VIEW_MODE_DEFAULT,
      'use_image_only' => self::USE_IMAGE_ONLY_DEFAULT,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $image_styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();

    $target_type = $this->fieldDefinition->getSetting('target_type');
    $view_modes = $this->entityDisplayRepository->getViewModes($target_type);

    $is_media_image_bundle = FALSE;
    if ($target_type === 'media') {
      $target_bundles = $this->fieldDefinition->getSetting('handler_settings')['target_bundles'] ?? [];
      if (in_array('image', $target_bundles, TRUE)) {
        $is_media_image_bundle = TRUE;
      }
    }

    $image_style_options = [
      'default' => 'Default',
    ];

    foreach ($image_styles as $image_style) {
      $image_style_options[$image_style->id()] = $image_style->label();
    }

    $view_mode_options = [];
    foreach ($view_modes as $view_mode => $info) {
      $view_mode_options[$view_mode] = $info['label'];
    }

    $form = [];

    $form['view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('View Mode'),
      '#default_value' => $this->getSetting('view_mode'),
      '#options' => $view_mode_options,
    ];

    if ($is_media_image_bundle) {
      $form['use_image_only'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Use image only'),
        '#default_value' => $this->getSetting('use_image_only'),
        '#description' => $this->t('Use image field, not rendered media entity'),
      ];
    }

    $form['image_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Image Style'),
      '#default_value' => $this->getSetting('image_style'),
      '#options' => $image_style_options,
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][use_image_only]"]' => ['checked' => TRUE],
        ],
      ],
      '#access' => $is_media_image_bundle,
    ];

    $form['show_alt_text'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show alt text'),
      '#default_value' => $this->getSetting('show_alt_text'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][use_image_only]"]' => ['checked' => TRUE],
        ],
      ],
      '#access' => $is_media_image_bundle,
    ];

    $form['show_full_screen'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Full screen mode'),
      '#default_value' => $this->getSetting('show_full_screen'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][use_image_only]"]' => ['checked' => TRUE],
        ],
      ],
      '#access' => $is_media_image_bundle,
    ];

    $form['show_indicators'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show indicators'),
      '#default_value' => $this->getSetting('show_indicators'),
    ];

    $form['show_controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show controls'),
      '#default_value' => $this->getSetting('show_controls'),
    ];

    $form['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval'),
      '#default_value' => $this->getSetting('interval'),
      '#min' => 1000,
      '#step' => 500,
      '#description' => $this->t('Set the interval in milliseconds between slide transitions.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if (!$this->getSetting('use_image_only')) {
      $summary[] = $this->t('View Mode: @mode', ['@mode' => $this->getSetting('view_mode')]);
    } else {
      $summary[] = $this->t('Use image only: @value', ['@value' => $this->getSetting('use_image_only') ? $this->t('Yes') : $this->t('No, use rendered entity')]);
      if ($this->getSetting('image_style') === 'default') {
        $summary[] = $this->t('Image Style: @style', ['@style' => 'Default']);
      } else {
        $summary[] = $this->t('Image Style: @style', ['@style' => $this->entityTypeManager->getStorage('image_style')->load($this->getSetting('image_style'))->label()]);
      }
      $summary[] = $this->t('Show alt text: @value', ['@value' => $this->getSetting('show_alt_text') ? $this->t('Yes') : $this->t('No')]);
      $summary[] = $this->t('Fullscreen mode: @value', ['@value' => $this->getSetting('show_full_screen') ? $this->t('Yes') : $this->t('No')]);
    }
    $summary[] = $this->t('Show indicators: @value', ['@value' => $this->getSetting('show_indicators') ? $this->t('Yes') : $this->t('No')]);
    $summary[] = $this->t('Show controls: @value', ['@value' => $this->getSetting('show_controls') ? $this->t('Yes') : $this->t('No')]);
    $summary[] = $this->t('Interval: @value ms', ['@value' => $this->getSetting('interval')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $config = \Drupal::config('bootstrap_toolbox_carousel.settings');
    $selected_library = $config->get('selected_library');
    $carousel_id = 'carousel-' . uniqid();

    if ($this->getSetting('use_image_only')) {
      $image_style = $this->getSetting('image_style');
      $images = [];
      foreach ($items as $delta => $item) {
        $media = Media::load($item->target_id);

        if ($media instanceof MediaInterface && $media->bundle() === 'image') {
          $image_field = $media->get('field_media_image');

          if (!$image_field->isEmpty()) {
            $uri = $image_field->entity->getFileUri();
            
            if ($image_style !== 'default') {
              $uri = ImageStyle::load($image_style)->buildUri($uri);
            }
            
            $images[] = [
              'uri' => $uri,
              'alt' => $item->alt,
            ];
          }
        }
      }
      
      $elements[] = [
        '#theme' => 'bootstrap_toolbox_media_images_carousel',
        '#images' => $images,
        '#carousel_id' => $carousel_id,
        '#settings' => $this->getSettings(),
        '#attached' => [
          'library' => [
            $selected_library,
          ],
        ],
      ];
      if ($this->getSetting('show_full_screen')) {
        $elements['#attached']['drupalSettings']['bootstrap_toolbox_carousel']['carousel_id'] = $carousel_id;
        $elements['#attached']['drupalSettings']['bootstrap_toolbox_carousel']['interval'] = $this->getSetting('interval');
        $elements['#attached']['library'][] = 'bootstrap_toolbox_carousel/bootstrap_carousel_full_screen';
      }
    } else {
      $entities = [];
      $entity_type = $this->fieldDefinition->getSetting('target_type');
      $view_mode = $this->getSetting('view_mode');

      foreach ($items as $delta => $item) {
        $entity = $this->entityTypeManager->getStorage($entity_type)->load($item->target_id);
        $render_array = $this->entityTypeManager->getViewBuilder($entity_type)->view($entity, $view_mode);
        $entities[$delta] = $render_array;
      }
      
      $elements[] = [
        '#theme' => 'bootstrap_toolbox_entities_carousel',
        '#entities' => $entities,
        '#carousel_id' => $carousel_id,
        '#settings' => $this->getSettings(),
        '#attached' => [
          'library' => [
            $selected_library,
          ],
        ],
      ];
    }
    
    return $elements;
  }

}
