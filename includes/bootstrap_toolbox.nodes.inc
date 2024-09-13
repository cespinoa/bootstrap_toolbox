<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module.
 *
 * Contains nodes related functions.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Implements hook_form_FORM_ID_alter() for node edit forms.
 *
 * Move Bootstrap Toolbox fields in a details container in advanced settings.
 */
function bootstrap_toolbox_form_node_form_alter(
  &$form,
  FormStateInterface $form_state,
  $form_id,
  ){
 
  if (isset($form['hide_sidebars'])
    || isset($form['hide_title'])
    || isset($form['edge_to_edge'])
    || isset($form['hide_breadcrumb'])
    || isset($form['custom_thext']) ){

    
    
    $form['bootstrap_toolbox'] = [
      '#type' => 'details',
      '#title' => t('Bootstrap Toolbox Settings'),
      '#group' => 'advanced',
      '#weight' => -999,
    ];
    $fields = [
      'override_node_settings',
      'hide_sidebars',
      'hide_title',
      'hide_breadcrumb',
      'edge_to_edge',
      'custom_theme',
    ];
    foreach($fields as $fieldName){
      if (isset($form[$fieldName])) {
        $form['bootstrap_toolbox'][$fieldName] = $form[$fieldName];
        unset($form[$fieldName]);
      }
    }
    $form['bootstrap_toolbox']['custom_theme']['#weight'] = 90;
    array_shift($fields);
    foreach($fields as $fieldName){
      $form['bootstrap_toolbox'][$fieldName]['#states'] = [
        'visible' => [
          ':input[name="override_node_settings[value]"]' =>
            ['checked' => TRUE],
        ],
      ];
    }
  }
}



/**
 * Set dynamic allowed values for the alignment field.
 *
 * @param \Drupal\field\Entity\FieldStorageConfig $definition
 *   The field definition.
 * @param \Drupal\Core\Entity\ContentEntityInterface|null $entity
 *   The entity being created if applicable.
 * @param bool $cacheable
 *   Boolean indicating if the results are cacheable.
 *
 * @return array
 *   An array of possible key and value options.
 *
 * @see options_allowed_values()
 */
function bootstrap_toolbox_allowed_values_function(FieldStorageConfig $definition,
  ContentEntityInterface $entity = NULL,
  $cacheable){

  $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
  return $utilityService->getAllowedThemes();
}
