<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Entity;

use Drupal\bootstrap_toolbox\BootstrapToolboxStyleInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the bootstrap toolbox style entity type.
 *
 * @ConfigEntityType(
 *   id = "bootstrap_toolbox_style",
 *   label = @Translation("Bootstrap Toolbox Style"),
 *   label_collection = @Translation("Bootstrap Toolbox Styles"),
 *   label_singular = @Translation("bootstrap toolbox style"),
 *   label_plural = @Translation("bootstrap toolbox styles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count bootstrap toolbox style",
 *     plural = "@count bootstrap toolbox styles",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bootstrap_toolbox\BootstrapToolboxStyleListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bootstrap_toolbox\Form\BootstrapToolboxStyleForm",
 *       "edit" = "Drupal\bootstrap_toolbox\Form\BootstrapToolboxStyleForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "bootstrap_toolbox_style",
 *   admin_permission = "administer bootstrap_toolbox_style",
 *   links = {
 *     "collection" = "/admin/config/bootstrap-toolbox/style",
 *     "add-form" = "/admin/config/bootstrap-toolbox/style/add",
 *     "edit-form" = "/admin/config/bootstrap-toolbox/style/{bootstrap_toolbox_style}",
 *     "delete-form" = "/admin/config/bootstrap-toolbox/style/{bootstrap_toolbox_style}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "clases",
 *     "scope",
 * "classes"
 *   },
 * )
 */
final class BootstrapToolboxStyle extends ConfigEntityBase implements BootstrapToolboxStyleInterface {

  /**
   * Style ID.
   */
  protected string $id;

  /**
   * Style label.
   */
  protected string $label;

  /**
   * Style description.
   */
  protected string $description;

  /**
   * Style classes.
   */
  protected string $classes;

  /**
   * Style scope.
   */
  protected array $scope;

  /**
   * Style description getter.
   *
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * Style classes getter.
   *
   * @return string
   */
  public function getClasses(): string {
    return \Drupal::service('bootstrap_toolbox.utility_service')->sanitizeTextField($this->classes);
  }

  /**
   * Style classes getter as array.
   *
   * @return array
   */
  public function getClassesAsArray(): array {
    $items = $this->getClasses();
    return explode(' ', $items);
  }

  /**
   * Style classes getter as HTML list.
   *
   * @return object
   */
  public function getClassesAsHtmlList(): object {
    $items = $this->getClassesAsArray();
    return \Drupal::service('bootstrap_toolbox.utility_service')->arrayToHtmlList($items);
  }

  /**
   * Style scope getter.
   *
   * @return array
   */
  public function getScope(): array {
    $selectedScopes = [];
    foreach ($this->scope as $scope => $selected) {
      if ($selected) {
        $selectedScopes[] = $selected;
      }
    }
    return $selectedScopes;
  }

  /**
   * Style scope HTML list.
   *
   * @return object
   */
  public function getScopeHtmlList(): object {
    $items = $this->getScope();
    $labels = [];
    foreach ($items as $item) {
      $labels[] = \Drupal::service('bootstrap_toolbox.utility_service')->getScopeLabel($item);
    }
    return \Drupal::service('bootstrap_toolbox.utility_service')->arrayToHtmlList($labels);
  }

}
