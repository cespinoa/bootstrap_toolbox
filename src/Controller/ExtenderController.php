<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Bootstrap Toolbox routes.
 */
final class ExtenderController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    //~ $config = \Drupal::service('config.factory')->getEditable('views.settings');
    //~ $display_extenders = $config->get('display_extenders') ?: array();
    //~ $display_extenders[] = 'bootstrap_toolbox';
    //~ $config->set('display_extenders', $display_extenders);
    //~ $config->save();


    //~ $config = \Drupal::service('config.factory')->getEditable('views.settings');
    //~ $display_extenders = $config->get('display_extenders') ?: array();
    //~ kint($display_extenders);
    //~ $key = array_search('bootstrap_toolbox_display_extender', $display_extenders);
    //~ if ($key!== FALSE) {
      //~ unset($display_extenders[$key]);
      //~ $config->set('display_extenders', $display_extenders);
      //~ $config->save();
      //~ kint($display_extenders);
    //~ }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];
    
    return $build;
  }

}
