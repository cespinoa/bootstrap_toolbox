<?php

namespace Drupal\bootstrap_toolbox\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;

class BootstrapToolboxThemeNegotiator implements ThemeNegotiatorInterface {


  /**
   * The utility service.
   *
   * @var Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected $utilityService;

  /**
   * The customTheme value.
   */
  protected $customTheme;

  /**
   * Constructs a ThemeNegotiator object.
   *
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface
   *   The utility service
   */
  public function __construct(
    UtilityServiceInterface $utilityService
    ){
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $customTheme = $this->utilityService->getBootstrapToolboxParameters()['custom_theme'];
    if($customTheme){
      $this->customTheme = $customTheme;
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
    
    return \Drupal::config('system.theme')->get('default');
  }

}

