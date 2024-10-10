<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a bootstrap toolbox style entity type.
 */
interface BootstrapToolboxStyleInterface extends ConfigEntityInterface {

/**
   * Style description getter.
   *
   * @return string
   */
  public function getDescription(): string;

  /**
   * Style classes getter.
   *
   * @return string
   */
  public function getClasses(): string;

  /**
   * Style classes getter as array.
   *
   * @return array
   */
  public function getClassesAsArray(): array;

  /**
   * Style classes getter as HTML list.
   *
   * @return object|string|null
   */
  public function getClassesAsHtmlList(): object|string|null;

  /**
   * Style scope getter.
   *
   * @return array
   */
  public function getScope(): array;

  /**
   * Style scope HTML list.
   *
   * @return object|string|null
   */
  public function getScopeHtmlList(): object|string|null;


}
