<?php
use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module with node types related functions.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 * 
 * Provide ThirdPartySetting to node bundles
 * 
 * Available settings under bootstrap_toolbox:
 * - hide_title
 * - hide_sidebars
 * - hide_breadcrumb
 * - edge_to_edge
 * 
 * 
 */
//~ function bootstrap_toolbox_form_node_type_edit_form_alter(&$form, Drupal\Core\Form\FormStateInterface $formstate, $formid) {
  
  
//~ }

/**
 * Entity builder for the node type form with ThirdPartySetting options.
 */
function bootstrap_toolbox_form_node_type_edit_form_builder($entityType, \Drupal\node\Entity\NodeType $type, &$form, FormStateInterface $formstate) {
  $type->setThirdPartySetting('bootstrap_toolbox', 'hide_title', $formstate->getValue('hide_title'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'hide_sidebars', $formstate->getValue('hide_sidebars'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'hide_breadcrumb', $formstate->getValue('hide_breadcrumb'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'edge_to_edge', $formstate->getValue('edge_to_edge'));
}




