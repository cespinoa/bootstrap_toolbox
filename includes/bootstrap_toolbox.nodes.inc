<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module with nodes related functions.
 */


/**
 * Implements hook_form_FORM_ID_alter() for node edit forms.
 *
 * Move Bootstrap Toolbox fields in a details container in advanced settings
 * 
 */
function bootstrap_toolbox_form_node_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $formstate, $formid) {
  
  if (isset($form['hide_sidebars']) || isset($form['hide_title']) || isset($form['edge_to_edge']) || isset($form['hide_breadcrumb'])) {
  
    $form['bootstrap_toolbox'] = [
      '#type' => 'details',
      '#title' => t('Bootstrap Toolbox Settings'),
      '#group' => 'advanced',
      '#weight' => -999,
    ];

    // Mueve los campos existentes al nuevo grupo de detalles.
    if (isset($form['override_node_settings'])) {
      $form['bootstrap_toolbox']['override_node_settings'] = $form['override_node_settings'];
      unset($form['override_node_settings']);
    }
    
    if (isset($form['hide_sidebars'])) {
      $form['bootstrap_toolbox']['hide_sidebars'] = $form['hide_sidebars'];
      unset($form['hide_sidebars']);
    }

    if (isset($form['hide_title'])) {
      $form['bootstrap_toolbox']['hide_title'] = $form['hide_title'];
      unset($form['hide_title']);
    }
    
    if (isset($form['hide_breadcrumb'])) {
      $form['bootstrap_toolbox']['hide_breadcrumb'] = $form['hide_breadcrumb'];
      unset($form['hide_breadcrumb']);
    }

    if (isset($form['edge_to_edge'])) {
      $form['bootstrap_toolbox']['edge_to_edge'] = $form['edge_to_edge'];
      unset($form['edge_to_edge']);
    }
    
    $node = $formstate->getFormObject()->getEntity();
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nodeType = \Drupal\node\Entity\NodeType::load($node->bundle());
      $thirdPartySettings = $nodeType->getThirdPartySettings('bootstrap_toolbox');
      if($thirdPartySettings){
        $form['bootstrap_toolbox']['hide_sidebars']['#states'] = [
          'visible' => [
            ':input[name="override_node_settings[value]"]' => ['checked' => TRUE],
          ],
        ];
         $form['bootstrap_toolbox']['hide_title']['#states'] = [
          'visible' => [
            ':input[name="override_node_settings[value]"]' => ['checked' => TRUE],
          ],
        ];
        $form['bootstrap_toolbox']['hide_breadcrumb']['#states'] = [
          'visible' => [
            ':input[name="override_node_settings[value]"]' => ['checked' => TRUE],
          ],
        ];
        $form['bootstrap_toolbox']['edge_to_edge']['#states'] = [
          'visible' => [
            ':input[name="override_node_settings[value]"]' => ['checked' => TRUE],
          ],
        ];
      }else{
        $form['bootstrap_toolbox']['override_node_settings']['#access'] = FALSE;
      }
    }
    
  }
}
