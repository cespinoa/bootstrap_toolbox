<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of bootstrap toolbox scopes.
 */
final class BootstrapToolboxScopeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Name');
    $header['id'] = $this->t('Machine name');
    $header['description'] = $this->t('Description');
    $header['system'] = $this->t('System scope');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->getDescription();
    $row['system'] = $entity->isSystem() ? $this->t('System') : NULL;
    return $row + parent::buildRow($entity);
  }

}
