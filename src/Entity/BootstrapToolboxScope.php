<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Entity;

use Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the bootstrap toolbox scope entity type.
 *
 * @ConfigEntityType(
 *   id = "bootstrap_toolbox_scope",
 *   label = @Translation("Bootstrap Toolbox Scope"),
 *   label_collection = @Translation("Bootstrap Toolbox Scopes"),
 *   label_singular = @Translation("bootstrap toolbox scope"),
 *   label_plural = @Translation("bootstrap toolbox scopes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count bootstrap toolbox scope",
 *     plural = "@count bootstrap toolbox scopes",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bootstrap_toolbox\BootstrapToolboxScopeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bootstrap_toolbox\Form\BootstrapToolboxScopeForm",
 *       "edit" = "Drupal\bootstrap_toolbox\Form\BootstrapToolboxScopeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "bootstrap_toolbox_scope",
 *   admin_permission = "administer bootstrap_toolbox_scope",
 *   links = {
 *     "collection" = "/admin/config/bootstrap-toolbox/scope",
 *     "add-form" = "/admin/config/bootstrap-toolbox/scope/add",
 *     "edit-form" = "/admin/config/bootstrap-toolbox/scope/{bootstrap_toolbox_scope}",
 *     "delete-form" = "/admin/config/bootstrap-toolbox/scope/{bootstrap_toolbox_scope}/delete",
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
 *     "system",
 *   },
 * )
 */
final class BootstrapToolboxScope extends ConfigEntityBase implements BootstrapToolboxScopeInterface {

  /**
   * The scope ID.
   */
  protected string $id;

  /**
   * The scope label.
   */
  protected string $label;

  /**
   * The scope description.
   */
  protected string $description;

  /**
   * The scope system condition.
   */
  protected bool $system = FALSE;

  /*
   * Gets scope description
   *
   * @return string
   *   The description.
   */
  public function getDescription(): string {
    return $this->description;
  }

  /*
   * Gets scope system condition
   *
   * @return bool
   *   The system condition.
   */
  public function isSystem(): bool {
    return $this->system;
  }

  /**
   * {@inheritdoc}
   */
  //~ public function delete() {
    //~ if ($this->get('system')) {
      //~ throw new \Drupal\Core\Entity\EntityStorageException(t('Cannot delete a system scope.'));
    //~ }
    //~ parent::delete();
  //~ }

}
