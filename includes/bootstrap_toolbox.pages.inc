<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module with pages related functions.
 */


/**
 * Implements hook_preprocess_HOOK() for page templates.
 * 
 * ThirdPartySetting and custom fields preprocess altering page visualization.
 * 
 */ 
function bootstrap_toolbox_preprocess_page(array &$variables) {

  $hideTitle = FALSE;
  $hideSidebars = FALSE;
  $hideBreadcrumb = FALSE;
  $edgeToEdge = FALSE;
  $forceToPanel = FALSE;

  $route_match = \Drupal::routeMatch();
  $route = $route_match->getRouteName();


  $routeMatch = \Drupal::service('current_route_match');
  $pageType = $routeMatch->getParameters()->keys()[0];
  

  $routeSettings = FALSE;
  $route = $route_match->getRouteObject();
  if ($route->getOption('bootstrap_toolbox') ) {
    $routeSettings = TRUE;
  }
  
  $config = \Drupal::config('bootstrap_toolbox.settings');
  
  $selectedTheme = $config->get('selected_theme');

  if($selectedTheme == 'custom'){
    $sidebarsVariables = $config->get('sidebars_variables');
    if($sidebarsVariables){
      $sidebarsVariables = explode(' ',$sidebarsVariables);  
    } else {
      $sidebarsVariables = '';
    }
    $mainAreaSelector = $config->get('main_area_selector');
    $mainAreaClass = $config->get('main_area_class');
    $centralPanelClass = $config->get('central_panel_class');
  } else {
    $themeVariables = \Drupal::service('bootstrap_toolbox.utility_service')->getThemeSelectors($selectedTheme);
    $sidebarsVariables = $themeVariables['sidebars_variables'];
    $mainAreaSelector = $themeVariables['main_area_selector'];
    $mainAreaClass = $themeVariables['main_area_class'];
    $centralPanelClass = $themeVariables['central_panel_class'];
  }
  
  $editModeHideSidebars = $config->get('edit_mode_hide_sidebars');
  $editModeEdgeToEdge = $config->get('edit_mode_edge_to_edge');

  if($pageType == 'node'){
    $node = $variables['node'];
    $nodeType = $node->bundle();
    $nodeSettings = \Drupal\node\Entity\NodeType::load($nodeType);
    $thirdPartySettings = NULL;

    if ($nodeSettings) {
      $thirdPartySettings = $nodeSettings->getThirdPartySettings('bootstrap_toolbox');
    }

    // Case 1: Node type with ThirdPartySetting
    if ($thirdPartySettings) {
      $hideSidebars = $thirdPartySettings['hide_sidebars'] ?? $hideSidebars;
      $hideTitle = $thirdPartySettings['hide_title'] ?? $hideTitle;
      $hideBreadcrumb = $thirdPartySettings['hide_breadcrumb'] ?? $hideTitle;
      $edgeToEdge = $thirdPartySettings['edge_to_edge'] ?? $edgeToEdge;

      // Individual node override ThirdPartySetting
      if (isset($node->override_node_settings) && $node->override_node_settings->value) {
        $hideSidebars = $node->hide_sidebars->value ?? $hideSidebars;
        $hideTitle = $node->hide_title->value ?? $hideTitle;
        $hideBreadcrumb = $node->hide_breadcrumb->value ?? $hideBreadcrumb;
        $edgeToEdge = $node->edge_to_edge->value ?? $edgeToEdge;
      }
    }
    // Case 2: Node type without ThirdPartySetting - Individual node settings
    else {
      if (isset($node->hide_sidebars)) {
        $hideSidebars = $node->hide_sidebars->value;
      }
      if (isset($node->hide_title)) {
        $hideTitle = $node->hide_title->value;
      }
      if (isset($node->hide_breadcrumb)) {
        $hideTitle = $node->hide_breadcrumb->value;
      }
      if (isset($node->edge_to_edge)) {
        $edgeToEdge = $node->edge_to_edge->value;
      }
    }
  } elseif($pageType == 'view_id'){
      $viewId = $routeMatch->getParameter('view_id');
      $displayId = $routeMatch->getParameter('display_id');
      $view = \Drupal\views\Views::getView($viewId);
      $view->setDisplay($displayId);
      $settings = $view->getDisplay()->options['display_extenders']['bootstrap_toolbox_display_extender'];
      $hideTitle = $settings['hide_title'] ?? FALSE;
      $hideSidebars = $settings['hide_sidebars'] ?? FALSE;
      $hideBreadcrumb = $settings['hide_breadcrumb'] ?? FALSE;
      $edgeToEdge = $settings['edge_to_edge'] ?? FALSE;
  } elseif($routeSettings){
      $settingsFromRoute = $route->getOption('bootstrap_toolbox');
      $hideTitle = $settingsFromRoute['hide_title'];
      $hideSidebars = $settingsFromRoute['hide_sidebars'];
      $hideBreadcrumb = $settingsFromRoute['hide_breadcrumb'];
      $edgeToEdge = $settingsFromRoute['edge_to_edge'];
  }
  
  // Case 3 Front page
  if($variables['is_front']){
    $frontPageOptions = $config->get('front_page_options');
    $hideTitle = $frontPageOptions['hide_title'];
    $hideSidebars = $frontPageOptions['hide_sidebars'];
    $hideBreadcrumb = $frontPageOptions['hide_breadcrumb'];
    $edgeToEdge = $frontPageOptions['edge_to_edge'];
  }

  // Case 4 Node edit mode
  $routeName = \Drupal::routeMatch()->getRouteName();
  $node = \Drupal::routeMatch()->getParameter('node');
  if (($routeName == 'entity.node.edit_form' && $node instanceof \Drupal\node\NodeInterface) ||
      ($routeName == 'node.add' && $formid == 'node_article_form') ) {
    $hideSidebars = FALSE;
    if($editModeHideSidebars){
      $hideSidebars = TRUE;
    }
    if($editModeEdgeToEdge == 'edge_to_edge'){
      $edgeToEdge = TRUE;
      $forceToPanel = FALSE;
    }elseif($editModeEdgeToEdge == 'central_panel'){
      $edgeToEdge = FALSE;
      $forceToPanel = TRUE;
    }
  }

  // Apply config
  if ($hideSidebars) {
    foreach($sidebarsVariables as $sidebarVariable){
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
  
  if($hideBreadcrumb){
    $variables['page']['breadcrumb'] = NULL;
  }

  if($edgeToEdge) {
    $variables['#attached']['library'][] = 'bootstrap_toolbox/apply_container_class';
    $variables['#attached']['drupalSettings']['apply_container_class'] = [
      'className' => $mainAreaClass,
      'elementSelector' => $mainAreaSelector,
      'action' => 'edge_to_edge',
    ];
  }

  if($forceToPanel){
    $variables['#attached']['library'][] = 'bootstrap_toolbox/apply_container_class';
    $variables['#attached']['drupalSettings']['apply_container_class'] = [
      'className' => $centralPanelClass,
      'elementSelector' => $mainAreaSelector,
      'action' => 'force_to_panel',
    ];
  }

  if($config->get('change_areas_width')){
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

  $variables['#attached']['library'][] = 'bootstrap_toolbox/remove_empty_paragraphs';

  //~ if(\Drupal::request()->query->get('mode')=='show_classess'){
    //~ foreach($variables['page'] as $key=>$region){
      //~ if(substr($key,0,1) != '#' && $key != 'content'){
        //~ $variables['page'][$key] = NULL;
      //~ }
    //~ }
  //~ }
  
   
  
}

