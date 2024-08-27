<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox_list_group\Controller;

use Drupal\Core\Controller\ControllerBase;


use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;


/**
 * Returns responses for Bootstrap Toolbox List_Group routes.
 */
final class BootstrapToolboxListGroupController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    
    
    
    
    
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];
    

    return $build;
  }

}
