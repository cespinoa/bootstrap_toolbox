<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of bootstraptoolboxwrappers.
 */
final class BootstrapToolboxWrapperListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['description'] = $this->t('Wrapper class');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxWrapperInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->get('description');
    return $row + parent::buildRow($entity);
  }

}
