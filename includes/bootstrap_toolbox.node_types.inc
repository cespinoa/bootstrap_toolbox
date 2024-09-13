<?php

/**
 * @file
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module with node types related functions.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * Provide ThirdPartySetting to node bundles.
 *
 * Available settings under bootstrap_toolbox:
 * - hide_title
 * - hide_sidebars
 * - hide_breadcrumb
 * - edge_to_edge
 * - custom_theme
 */

/**
 * Implemented in bootrap_toolbox_blocks.inc.
 *
 * Function bootstrap_toolbox_form_node_type_edit_form_alter(&$form, Drupal\Core\Form\FormStateInterface $form_state, $formid) {
 *
 * }
 * */

/**
 * Entity builder for the node type form with ThirdPartySetting options.
 */
function bootstrap_toolbox_form_node_type_edit_form_builder(
  $entityType,
  NodeType $type,
  &$form,
  FormStateInterface $form_state,
) {
  $type->setThirdPartySetting('bootstrap_toolbox', 'hide_title', $form_state->getValue('hide_title'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'hide_sidebars', $form_state->getValue('hide_sidebars'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'hide_breadcrumb', $form_state->getValue('hide_breadcrumb'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'edge_to_edge', $form_state->getValue('edge_to_edge'));
  $type->setThirdPartySetting('bootstrap_toolbox', 'custom_theme', $form_state->getValue('custom_theme'));
}
