<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a bootstrap toolbox scope entity type.
 */
interface BootstrapToolboxScopeInterface extends ConfigEntityInterface {

  /*
   * Gets scope description
   *
   * @return string
   *   The description.
   */
  public function getDescription(): string;

  /*
   * Gets scope system condition
   *
   * @return bool
   *   The system condition.
   */
  public function isSystem(): bool;

}
