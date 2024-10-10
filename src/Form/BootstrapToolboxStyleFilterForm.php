<?php

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Bootstrap Toolbox Style Filter Form.
 */
class BootstrapToolboxStyleFilterForm extends FormBase {

  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected $utilityService;

  /**
   * Constructs a BootstrapToolboxStyleFilterForm object.
   *
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *   The utility service.
   */
  public function __construct(UtilityServiceInterface $utilityService) {
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bootstrap_toolbox_style_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['scope'] = [
      '#type' => 'select',
      '#title' => $this->t('Scope'),
      '#options' => $this->utilityService->getScopeList(),
      '#empty_option' => $this->t('- Any -'),
      '#default_value' => $this->getRequest()->query->get('scope', ''),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Filter'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = [];
    if ($scope = $form_state->getValue('scope')) {
      $query['scope'] = $scope;
    }
    $form_state->setRedirect('entity.bootstrap_toolbox_style.collection', [], ['query' => $query]);
  }

}
