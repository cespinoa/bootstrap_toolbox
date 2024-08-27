<?php
namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;


class BootstrapToolboxStyleFilterForm extends FormBase {

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
  public function getFormId() {
    return 'bootstrap_toolbox_style_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $formstate): array {
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
  public function submitForm(array &$form, FormStateInterface $formstate) {
    $query = [];
    if ($scope = $formstate->getValue('scope')) {
      $query['scope'] = $scope;
    }
    $formstate->setRedirect('entity.bootstrap_toolbox_style.collection', [], ['query' => $query]);
  }


}
