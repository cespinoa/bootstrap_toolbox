<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bootstrap_toolbox\Entity\BootstrapToolboxScope;

/**
 * Bootstrap Toolbox Scope form.
 */
final class BootstrapToolboxScopeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $formstate): array {

    $form = parent::form($form, $formstate);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [BootstrapToolboxScope::class, 'load'],
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $this->entity->get('description'),
    ];

    $form['system'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('System scope'),
      '#default_value' => $this->entity->get('system')?? FALSE,
    ];

    if ($this->entity->get('system')) {
      // Remove the delete button if it's a system scope.
      unset($form['actions']['delete']);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $formstate): int {
    $result = parent::save($form, $formstate);
    $messageArgs = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        \SAVED_NEW => $this->t('Created new example %label.', $messageArgs),
        \SAVED_UPDATED => $this->t('Updated example %label.', $messageArgs),
      }
    );
    $formstate->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
