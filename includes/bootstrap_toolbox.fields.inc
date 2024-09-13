<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module.
 *
 * Fields related functions.
 */

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_field_formatter_third_party_settings_form().
 *
 * This hook is used to add third-party settings to all field formatters.
 */
function bootstrap_toolbox_field_formatter_third_party_settings_form(
  FormatterInterface $plugin,
  FieldDefinitionInterface $fieldDefinition,
  $viewMode,
  array $form,
  FormStateInterface $formState,
) {
  $element = [];

  $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
  $wrapperOptions = $utilityService
    ->getWrapperList();
  $classesOptions = $utilityService
    ->getScopeListFiltered(['field_formatters']);
  $wrapperClassesOptions = $utilityService
    ->getScopeListFiltered(['wrapper_formatters']);

  $settings = $plugin
    ->getThirdPartySetting('bootstrap_toolbox', 'settings');

  $element['settings'] = [
    '#type' => 'details',
    '#title' => 'Bootstrap Toolbox',
  ];

  $element['settings']['wrapper'] = [
    '#type' => 'select',
    '#title' => t('Wrapper'),
    '#options' => $wrapperOptions,
    '#empty_option' => 'None',
    '#default_value' => $settings['wrapper'] ?? 'none',
    '#attributes' => [
      'control-name' => 'wrapper',
    ],
  ];

  $element['settings']['wrapper_style'] = [
    '#type' => 'select',
    '#title' => t('Wrapper style'),
    '#options' => $wrapperClassesOptions,
    '#empty_option' => 'None',
    '#default_value' => $settings['wrapper_style'] ?? 'none',
    '#states' => [
      'invisible' => [
        ':input[control-name="wrapper"]' => ['value' => 'none'],
      ],
    ],
  ];

  $element['settings']['style'] = [
    '#type' => 'select',
    '#title' => t('Element style'),
    '#options' => $classesOptions,
    '#empty_option' => 'None',
    '#default_value' => $settings['style'] ?? 'none',
  ];

  return $element;
}

/**
 * Implements hook_field_formatter_settings_summary_alter().
 */
function bootstrap_toolbox_field_formatter_settings_summary_alter(
  array &$summary,
  $context,
) {
  if ($context['formatter']->getThirdPartySettings('bootstrap_toolbox')) {
    $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');

    $wrapperOptions = $utilityService
      ->getWrapperList();
    $wrapperStyleOptions = $utilityService
      ->getScopeListFiltered(['wrapper_formatters']);
    $styleOptiones = $utilityService
      ->getScopeListFiltered(['field_formatters']);

    $settings = $context['formatter']
      ->getThirdPartySetting('bootstrap_toolbox', 'settings');
    $wrapper = $settings['wrapper'];
    $wrapperStyle = $settings['wrapper_style'];
    $style = $settings['style'];

    if ($wrapper && $wrapper != 'none') {
      $summary[] = t('Wrapper: @wrapper', ['@wrapper' => $wrapperOptions[$wrapper]]);
      if ($wrapperStyle && $wrapperStyle != 'none') {
        $summary[] = t('Wrapper style: @wrapper_style', [
          '@wrapper_style' =>
          $wrapperStyleOptions[$wrapperStyle],
        ]);
      }
    }
    if ($style && $style != 'none') {
      $summary[] = t('Element style: @element_style', [
        '@element_style' =>
        $styleOptiones[$style],
      ]);
    }
  }
}
