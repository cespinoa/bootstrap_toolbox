<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;

/**
 * Returns responses for Bootstrap Toolbox routes.
 */
final class ReportController extends ControllerBase {

  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected UtilityServiceInterface $utilityService;

  /**
   * Constructs a ShowClassesController object.
   *
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *   The request stack service.
   */
  public function __construct(UtilityServiceInterface $utilityService) {
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): ReportController {
    return new static(
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    
    
    $output = [];
    $rows = [];

    $output['title_base_theme_section'] = [
      '#type' => 'markup',
      '#markup' => '<h2>' . $this->t('Available themes') . '</h2>',
    ];

    $output['description_base_theme_section'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('These are the themes enabled for use by site editors.') . '</p>',
    ];

    $output['description_base_theme_section_2'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Each of these themes must have an associated base theme that informs Bootstrap Toolbox of the theme\'s features.') . '</p>',
    ];

    $header = [
      $this->t('Theme'),
      $this->t('Base theme'),
    ];

    $allowedThemes = $this->utilityService->getAllowedThemes();
    $baseThemes = $this->utilityService->getBaseThemes();
    
    foreach($allowedThemes as $themeId => $themeName){
      $themeConfig = $this->utilityService->getThemeConfig($themeId);
      $baseTheme = NULL;
      if(array_key_exists('third_party_settings', $themeConfig) &&
         array_key_exists('bootstrap_toolbox', $themeConfig['third_party_settings'])
         ){
        $rows[] = [ $themeName, $baseThemes[$themeConfig['third_party_settings']['bootstrap_toolbox']['base_theme']] ];
      }
      else {
        $url = 'admin/appearance/settings/' . $themeId;
        $text = $this->t('You need to select a base theme')->__toString();
        $link = $this->utilityService->getLinkFromTextAndUrl($text,$url);
        $link = $this->utilityService->createMarkup($link);
        $rows[] = [ $themeName, $link ];
      }
    }
    
    $output['table__base_theme_section'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    $output['title_custom_fields_section'] = [
      '#type' => 'markup',
      '#markup' => '<h2>' . $this->t('Custom fields') . '</h2>',
    ];
    
    $output['description_custom_fields_section'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('These are the content types that Bootstrap Toolbox custom fields are associated with.') . '</p>',
    ];

    $text = $this->t('Extrafield node settings');
    $url = 'admin/config/bootstrap-toolbox/node-fields';
    $link = $this->utilityService->getLinkFromTextAndUrl($text,$url);
    $text = $this->t('You can configure custom fields in');

    $output['description_custom_fields_section_2'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $text . ' ' . $link . '</p>',
    ];
    

    $header = [
      $this->t('Node type'),
      $this->t('Has custom fields'),
    ];

    $rows = [];
    $config = $this->utilityService->getBootstrapToolboxSettings() ?? [];
    if(array_key_exists('selectedNodeTypes', $config)){
      $selectedNodeTypes = $config['selectedNodeTypes'];
      foreach($selectedNodeTypes as $nodeType => $value) {
        $rows[] = [
          $this->utilityService->getNodeTypeLabel($nodeType),
          $value ? $this->t('Yes') : $this->t('No'),
        ];
      }
    }
    
    $output['table_custom_fields_section'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    
    return $output;
  }

}
