<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\bootstrap_toolbox\Entity\BootstrapToolboxScope;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface;

/**
 * Bootstrap Toolbox Scope form.
 */
final class BootstrapToolboxScopeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {

    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface $entity **/
    $entity = $this->entity;

    $form = parent::form($form, $form_state);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => [BootstrapToolboxScope::class, 'load'],
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $entity->get('description'),
    ];

    $form['system'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('System scope'),
      '#default_value' => $entity->get('system') ?? FALSE,
    ];

    if ($entity->get('system')) {
      // Remove the delete button if it's a system scope.
      unset($form['actions']['delete']);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);
    $messageArgs = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        \SAVED_NEW => $this->t('Created new scope %label.', $messageArgs),
        \SAVED_UPDATED => $this->t('Updated scope %label.', $messageArgs),
        default => $this->t('Performed an action on scope %label.', $messageArgs),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
