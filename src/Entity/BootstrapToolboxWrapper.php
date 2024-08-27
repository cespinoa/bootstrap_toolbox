<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Entity;

use Drupal\bootstrap_toolbox\BootstrapToolboxWrapperInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the bootstrap_toolbox_wrapper entity type.
 *
 * @ConfigEntityType(
 *   id = "bootstrap_toolbox_wrapper",
 *   label = @Translation("Bootstrap Toolbox Wrapper"),
 *   label_collection = @Translation("Wrappers"),
 *   label_singular = @Translation("wrapper"),
 *   label_plural = @Translation("wrappers"),
 *   label_count = @PluralTranslation(
 *     singular = "@count wrapper",
 *     plural = "@count wrappers",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bootstrap_toolbox\BootstrapToolboxWrapperListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bootstrap_toolbox\Form\BootstrapToolboxWrapperForm",
 *       "edit" = "Drupal\bootstrap_toolbox\Form\BootstrapToolboxWrapperForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "bootstrap_toolbox_wrapper",
 *   admin_permission = "administer bootstrap_toolbox_wrapper",
 *   links = {
 *     "collection" = "/admin/config/bootstrap-toolbox/wrapper",
 *     "add-form" = "/admin/config/bootstrap-toolbox/wrapper/add",
 *     "edit-form" = "/admin/config/bootstrap-toolbox/wrapper/{bootstrap_toolbox_wrapper}",
 *     "delete-form" = "/admin/config/bootstrap-toolbox/wrapper/{bootstrap_toolbox_wrapper}/delete",
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
 *   },
 * )
 */
final class BootstrapToolboxWrapper extends ConfigEntityBase implements BootstraptoolboxwrapperInterface {

  /**
   * The example ID.
   */
  protected string $id;

  /**
   * The example label.
   */
  protected string $label;

  /**
   * The example description.
   */
  protected string $description;

}
