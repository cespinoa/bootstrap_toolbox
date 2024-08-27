<?php
/**
 * Implements hook_help().
 */
function bootstrap_toolbox_help($routeName, \Drupal\Core\Routing\RouteMatchInterface $routeMatch) {
  switch ($routeName) {
    // This is the route name for the module's main help page.
    case 'help.page.bootstrap_toolbox':
      return '<p>' . t('Bootstrap Tool Box allows you to control the display of the page title, sidebars, and breadcrumb, as well as display the page edge-to-edge.') . '</p>' .
             '<p>' . t('This control is done at the content type and node level. So you can set a certain bundle to be displayed edge-to-edge, but a particular node with that bundle type can be displayed in a container with a fixed size.') . '</p>';
      break;
    case 'help.page':
      return '';
      break;
  }
}




