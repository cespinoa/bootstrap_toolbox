<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bootstrap_toolbox\Entity\BootstrapToolboxStyle;

use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bootstrap Toolbox Style form.
 */
final class BootstrapToolboxStyleForm extends EntityForm {

  protected $utilityService;

  public function __construct(UtilityServiceInterface $utilityservice) {
    $this->utilityService = $utilityservice;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  
  
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $formstate): array {

    $form = parent::form($form, $formstate);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [BootstrapToolboxStyle::class, 'load'],
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $this->entity->get('description'),
    ];

    $classes = $this->getRequest()->query->get('classes', '');
    if(!$classes){
      $classes = $this->entity->get('classes');
    }

    $form['classes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Classes'),
      '#default_value' => $classes,
    ];

    $form['scope'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Scope'),
      '#default_value' => $this->entity->get('scope'),
      '#options' => $this->utilityService->getScopeList(),
    ];


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
        \SAVED_NEW => $this->t('Created new style %label.', $messageArgs),
        \SAVED_UPDATED => $this->t('Updated style %label.', $messageArgs),
      }
    );
    $formstate->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }



}
