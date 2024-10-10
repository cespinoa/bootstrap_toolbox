<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\bootstrap_toolbox\Entity\BootstrapToolboxStyle;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bootstrap Toolbox Style form.
 */
final class BootstrapToolboxStyleForm extends EntityForm {

  

  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected UtilityServiceInterface $utilityService;

  /**
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *  The utility service.
   * */
  public function __construct(UtilityServiceInterface $utilityService) {
    $this->utilityService = $utilityService;
  }

  /**
   * Inherit.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {

    $form = parent::form($form, $form_state);

    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface $entity **/
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => [BootstrapToolboxStyle::class, 'load'],
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $entity->get('description'),
    ];

    $classes = $this->getRequest()->query->get('classes', '');
    if (!$classes) {
      $classes = $entity->get('classes');
    }

    $form['classes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Classes'),
      '#default_value' => $classes,
    ];

    $form['scope'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Scope'),
      '#default_value' => $entity->get('scope'),
      '#options' => $this->utilityService->getScopeList(),
    ];

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
        \SAVED_NEW => $this->t('Created new style %label.', $messageArgs),
        \SAVED_UPDATED => $this->t('Updated style %label.', $messageArgs),
        default => $this->t('Performed an action on style %label.', $messageArgs),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
