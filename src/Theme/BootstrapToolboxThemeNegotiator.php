<?php

namespace Drupal\bootstrap_toolbox\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;

class BootstrapToolboxThemeNegotiator implements ThemeNegotiatorInterface {


  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected UtilityServiceInterface $utilityService;

  /**
   * The customTheme value.
   */
  protected string $customTheme;

  

  /**
   * Constructs a ThemeNegotiator object.
   * 
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *  The utility service.
   * */
  public function __construct(UtilityServiceInterface $utilityService) {
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $params = $this->utilityService->getBootstrapToolboxParameters();
    if($params && array_key_exists('custom_theme',$params) && $params['custom_theme']){
      $this->customTheme = $params['custom_theme'];
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    
    if (isset($this->customTheme)){
      return $this->customTheme;
    }
    
    return $this->utilityService->getDefaultTheme();
    
  }

}

