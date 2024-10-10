<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module with pages related functions.
 */

use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\views\Views;
use Drupal\bootstrap_toolbox\Theme;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Template\Attribute;


/**
 * Implements hook_preprocess_html
 *
 * ThirdPartySetting and custom fields preprocess altering page visualization.
 */
function bootstrap_toolbox_preprocess_html(array &$variables) {
  
  $config = \Drupal::config('bootstrap_toolbox.settings');
  $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
  $params = $utilityService->getBootstrapToolboxParameters();
  $behaviorSelectors = $utilityService->getBehaviorSelectors();
  $variables['attributes'] = new Attribute();
  $variables['attributes']->addClass('base-theme-' . $behaviorSelectors);
  
  if ($params) {
    $edgeToEdge = !empty($params['edgeToEdge']); // Casting to boolean.
    $hideSidebars = !empty($params['hideSidebars']); // Casting to boolean.
    if ($edgeToEdge && $hideSidebars) {
      $variables['attributes']->addClass('bt-mode-landpage');
    } 
    elseif ($edgeToEdge) {
      $variables['attributes']->addClass('bt-edge-to-edge-with-sidebars');
    } 
    elseif ($hideSidebars) {
      $variables['attributes']->addClass('bt-panel-without-sidebars');
    }
  }
}



/**
 * Implements hook_preprocess_HOOK() for page templates.
 *
 * ThirdPartySetting and custom fields preprocess altering page visualization.
 */
function bootstrap_toolbox_preprocess_page(array &$variables) {

  $publicPath = \Drupal::service('file_system')->realpath('public://');
  $fileUrl = $publicPath . '/bootstrap_toolbox/custom.css';
  if (file_exists($fileUrl)) {
    $variables['#attached']['library'][] = 'bootstrap_toolbox/custom';
  }

  $config = \Drupal::config('bootstrap_toolbox.settings');
  $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
  $params = $utilityService->getBootstrapToolboxParameters();
  if(!$params){
    return;
  }
  
  $hideSidebars = $params['hideSidebars'];
  $hideTitle = $params['hideTitle'];
  $hideBreadcrumb = $params['hideBreadcrumb'];
  $edgeToEdge = $params['edgeToEdge'];
  $routeName = $params['routeName'];
  $node = $params['node'];
  $customTheme = $params['customTheme'];
  

  

  
  $isEditMode = FALSE;
  $forceToPanel = FALSE;
  // If node edit mode.
  if (isset($routeName)){
    if (($routeName == 'entity.node.edit_form' && $node instanceof NodeInterface) ||
        $routeName == 'node.add') {
      $editModeHideSidebars = $config->get('edit_mode_hide_sidebars');
      $editModeEdgeToEdge = $config->get('edit_mode_edge_to_edge');
      $hideSidebars = $editModeHideSidebars ?? FALSE;
      if ($editModeEdgeToEdge == 'edge_to_edge') {
        $edgeToEdge = TRUE;
        $forceToPanel = FALSE;
      }
      elseif ($editModeEdgeToEdge == 'central_panel') {
        $edgeToEdge = FALSE;
        $forceToPanel = TRUE;
      }
      $isEditMode = TRUE;
    }
  }

  
  // Get behavior selectors
  $behaviorSelectors = $utilityService->getBehaviorSelectors();
  if(!$behaviorSelectors){
    return;
  }
  
  $themeVariables = $utilityService->getThemeSelectors($behaviorSelectors);
  

  if(!$themeVariables){
    return;
  }
  
  $sidebarsVariables = $themeVariables['sidebars_variables'];
  $mainAreaSelector = $themeVariables['main_area_selector'];
  $mainAreaClass = $themeVariables['main_area_class'];
  $centralPanelClass = $themeVariables['central_panel_class'];
  
  // Apply config.
  if ($hideSidebars && $sidebarsVariables) {
    foreach ($sidebarsVariables as $sidebarVariable) {
      $variables['page'][$sidebarVariable] = NULL;
    }
  }

  if ($hideTitle) {
    foreach ($variables['page']['content'] as $key => $value) {
      if (strpos($key, '_page_title') !== FALSE) {
        $variables['page']['content'][$key] = NULL;
      }
    }
  }

  if ($hideBreadcrumb) {
    $variables['page']['breadcrumb'] = NULL;
  }

  if ($edgeToEdge && $mainAreaClass && $mainAreaSelector) {
    $variables['#attached']['library'][] = 'bootstrap_toolbox/apply_container_class';
    $variables['#attached']['drupalSettings']['apply_container_class'] = [
      'className' => $mainAreaClass,
      'elementSelector' => $mainAreaSelector,
      'action' => 'edge_to_edge',
    ];
  }

  // Set create and edit node form when use site theme
  if(!isAdminTheme() && $isEditMode){
    if ($forceToPanel) {
      $variables['#attached']['library'][] = 'bootstrap_toolbox/apply_container_class';
      $variables['#attached']['drupalSettings']['apply_container_class'] = [
        'className' => $centralPanelClass,
        'elementSelector' => $mainAreaSelector,
        'action' => 'force_to_panel',
      ];
    }

    if ($config->get('change_areas_width')) {
      $vars = [
          [
            'element' => $config->get('edit_mode_fields_area'),
            'class_to_remove' => $config->get('edit_mode_fields_area_remove_class'),
            'class_to_add' => $config->get('edit_mode_fields_area_add_class'),
          ],
          [
            'element' => $config->get('edit_mode_advanced_area'),
            'class_to_remove' => $config->get('edit_mode_advanced_area_remove_class'),
            'class_to_add' => $config->get('edit_mode_advanced_area_add_class'),
          ],
      ];
      $variables['#attached']['library'][] = 'bootstrap_toolbox/apply_edition_mode';
      $variables['#attached']['drupalSettings']['apply_edition_mode'] = $vars;
    }

  }
  
}

/**
 * Check if current theme is admin theme
 *
 * @return bool
 * 
  */
function isAdminTheme() {
  $theme_manager = \Drupal::service('theme.manager');
  $current_theme = $theme_manager->getActiveTheme()->getName();
  $admin_theme = \Drupal::config('system.theme')->get('admin');
  return $current_theme === $admin_theme;
}
