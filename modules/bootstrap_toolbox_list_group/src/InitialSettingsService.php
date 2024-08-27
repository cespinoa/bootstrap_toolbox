<?php

namespace Drupal\bootstrap_toolbox_list_group;

/**
 * Class InitialSettingsService.
 *
 * Providing some initial settings for the module, to avoid hardcoding and in
 * .module file and allow for easier extending to other formatters and sources
 */
class InitialSettingsService {

  /**
   * @var array of field formatter plugin ids
   */
  protected $formatters = ['entity_reference_label'];

  
  /**
   * Constructs a new InitialSettingsService object.
   */
  public function __construct() {

  }

  /**
   * @return array
   */
  public function getFormatters() {
    return $this->formatters;
  }

  

}
