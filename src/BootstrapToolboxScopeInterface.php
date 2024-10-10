<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a bootstrap toolbox scope entity type.
 */
interface BootstrapToolboxScopeInterface extends ConfigEntityInterface {

  /**
   * Gets scope description.
   *
   * @return string
   *   The description.
   */
  public function getDescription(): string;

  /**
   * Gets scope system condition.
   *
   * @return bool
   *   The system condition.
   */
  public function isSystem(): bool;

  /**
     * Returns the value of a property.
     *
     * @param string $property_name
     *   The name of the property that should be returned.
     *
     * @return mixed
     *   The property if it exists, or NULL otherwise.
     */
    public function get($property_name);

}
