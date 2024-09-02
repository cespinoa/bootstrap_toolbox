<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module with fields related functions.
 */

/**
 * Implements hook_field_formatter_third_party_settings_form().
 * 
 * This hook is used to add additional configurations (third-party settings) to all field formatters.
 * When the page is rendered edge-to-edge, you may want to keep some elements in a standard container
 * and alternate between blocks that render full width and blocks that remain centered.
 * Additionally, it allows you to add extra classes.
 * 
 * This is an alternative way to define the hook:
 * function bootstrap_toolbox_field_formatter_third_party_settings_form($context){}
 * 
 */
function bootstrap_toolbox_field_formatter_third_party_settings_form(\Drupal\Core\Field\FormatterInterface $plugin, \Drupal\Core\Field\FieldDefinitionInterface $fieldDefinition, $viewMode, array $form, \Drupal\Core\Form\FormStateInterface $formState) {
  $element = [];
  
  $utilityservice = \Drupal::service('bootstrap_toolbox.utility_service');
  $wrapperOptions = $utilityservice->getWrapperList();
  $classesOptions = $utilityservice->getScopeListFiltered(['field_formatters']);
  $wrapperClassesOptions = $utilityservice->getScopeListFiltered(['wrapper_formatters']);
  
  $settings = $plugin->getThirdPartySetting('bootstrap_toolbox','settings');
  
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
function bootstrap_toolbox_field_formatter_settings_summary_alter(array &$summary, $context) {
  if($context['formatter']->getThirdPartySettings('bootstrap_toolbox')){
    $utilityservice = \Drupal::service('bootstrap_toolbox.utility_service');

    $wrapperOptions = $utilityservice->getWrapperList();
    $wrapperStyleOptions = $utilityservice->getScopeListFiltered(['wrapper_formatters']);
    $styleOptiones = $utilityservice->getScopeListFiltered(['field_formatters']);
    
    $settings = $context['formatter']->getThirdPartySetting('bootstrap_toolbox','settings');
    $wrapper = $settings['wrapper'];
    $wrapperStyle = $settings['wrapper_style'];
    $style = $settings['style'];

    if($wrapper && $wrapper!='none'){
      $summary[] = t('Wrapper: @wrapper', ['@wrapper' => $wrapperOptions[$wrapper]]);
      if($wrapperStyle && $wrapperStyle!='none'){
        $summary[] = t('Wrapper style: @wrapper_style', ['@wrapper_style' => $wrapperStyleOptions[$wrapperStyle]]);
      }
    }
    if($style && $style!='none'){
      $summary[] = t('Element style: @element_style', ['@element_style' => $styleOptiones[$style]]);  
    }
  }
}


