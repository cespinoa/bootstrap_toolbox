<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Admin extrafields in node types.
 */
final class NodesExtraFieldsSettingForm extends ConfirmFormBase {

  /**
   * The utility service.
   *
   * @var Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected $utilityService;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a new NodesExtraFieldsSettingForm object.
   *
   * @param Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *   The Utility service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   */
  public function __construct(UtilityServiceInterface $utilityService, ModuleHandlerInterface $moduleHandler) {
    $this->utilityService = $utilityService;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service'),
      $container->get('module_handler')
    );
  }

  /**
   * The submitted action needing to be confirmed.
   *
   * @var array
   */
  protected $nodeTypesToAdd = [];

  /**
   * The submitted data needing to be confirmed.
   *
   * @var array
   */
  protected $nodeTypesToRemove = [];

  /**
   * The submitted data needing to be confirmed.
   *
   * @var array
   */
  protected $selectedNodeTypes = [];

  /**
   * The submitted data needing to be confirmed.
   *
   * @var string
   */
  protected $action = '';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // When this is the confirmation step fall through to the confirmation form.
    if ($this->action) {
      return parent::buildForm($form, $form_state);
    }

    $config = $this->config('bootstrap_toolbox.settings');

    $msg0 = $this->t('Bootstrap Toolbox will add the following fields to the checked node types');
    $msg1 = $this->t('Hide title');
    $msg2 = $this->t('Hide sidebars');
    $msg3 = $this->t('Hide breadcrumb');
    $msg4 = $this->t('Display edge to edge');
    $msg5 = $this->t('Select theme');
    $msg6 = $this->t('Generate table of content');
    $msg7 = $this->t('Be careful! If you uncheck any node type,
      their custom fields will be removed and the information contained
      in them will be lost for this node type.');

    $markup = [];
    $markup[] = '<div class="card">';
    $markup[] = '<p>' . $msg0 . ':</p>';
    $markup[] = '<ul>';
    $markup[] = '<li>' . $msg1 . '</li>';
    $markup[] = '<li>' . $msg2 . '</li>';
    $markup[] = '<li>' . $msg3 . '</li>';
    $markup[] = '<li>' . $msg4 . '</li>';
    $markup[] = '<li>' . $msg5 . '</li>';
    if ($this->moduleHandler->moduleExists('bt_toc')) {
      $markup[] = '<li>' . $msg6 . '</li>';
    }
    $markup[] = '</ul>';
    $markup[] = '<p>' . $msg7 . '</p>';
    $markup[] = '</div>';
    $markup = implode("\n", $markup);

    $form['help_text'] = [
      '#type' => 'markup',
      '#markup' => $markup,
    ];

    $form['selectedNodeTypes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select Node Types'),
      '#default_value' => $config->get('selectedNodeTypes') ? $config->get('selectedNodeTypes') : [],
      '#options' => $this->utilityService->getNodeTypes(),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Confirm'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bootstrap_toolbox_nodes_extrafields';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    return $this->t('Are you sure you want to do this?');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    $nodeTypes = $this->utilityService->getNodeTypes();
    $deleteText = '';
    $createText = '';
    $strings = [
      '@warning' => 'Attention!',
      '@msg_delete_1' => 'Boostrap Toolbox custom fields will be removed from the following node types',
      '@msg_delete_2' => 'This action cannot be undone',
      '@msg_create' => 'Boostrap Toolbox custom fields will be added to the following node types',
    ];

    if ($this->nodeTypesToRemove && count($this->nodeTypesToRemove)) {
      $deleteText = $this->utilityService->buildDescriptionList($nodeTypes, $this->nodeTypesToRemove, 'remove');
    }
    if ($this->nodeTypesToAdd && count($this->nodeTypesToAdd)) {
      $createText = $this->utilityService->buildDescriptionList($nodeTypes, $this->nodeTypesToAdd, 'add');
    }

    return $this->t($deleteText . $createText, $strings);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('bootstrap_toolbox.node_fields.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {

    if (!$this->action) {
      $form_state->setRebuild();
      $oldConfig = $this->config('bootstrap_toolbox.settings');
      $oldNodeTypes = $oldConfig->get('selectedNodeTypes') ? $oldConfig->get('selectedNodeTypes') : [];
      $newNodeTypes = $form_state->getValue('selectedNodeTypes') ? $form_state->getValue('selectedNodeTypes') : [];

      $nodeTypesToAdd = array_diff($newNodeTypes, $oldNodeTypes);
      $nodeTypesToRemove = array_diff($oldNodeTypes, $newNodeTypes);

      $this->nodeTypesToAdd = $this->utilityService->filterNonZeroValues($nodeTypesToAdd);
      $this->nodeTypesToRemove = $this->utilityService->filterNonZeroValues($nodeTypesToRemove);
      $this->selectedNodeTypes = $newNodeTypes;
      $this->action = 'Continue';
      return;
    }
    $this->{'processFields'}();

  }

  /**
   * Process fields .
   */
  protected function processFields(): void {
    $nodeTypesToRemove = [];
    $nodeTypesToAdd = [];

    if ($this->nodeTypesToRemove && count($this->nodeTypesToRemove)) {
      foreach ($this->nodeTypesToRemove as $key => $value) {
        $nodeTypesToRemove[] = $key;
      }
    }
    if ($this->nodeTypesToAdd && count($this->nodeTypesToAdd)) {
      foreach ($this->nodeTypesToAdd as $key => $value) {
        $nodeTypesToAdd[] = $key;
      }
    }

    // Add fields starting.
    $fieldnames = [
      'override_node_settings' => [
        'label'   => 'Override node settings',
        'description' => 'Override the default configuration',
        'type' => 'boolean',
      ],
      'hide_sidebars' => [
        'label'   => 'Hide sidebars',
        'description' => 'Hide sidebars when this node is displayed',
        'type' => 'boolean',
      ],
      'hide_title' => [
        'label'   => 'Hide title',
        'description' => 'Hide title when this node is displayed',
        'type' => 'boolean',
      ],
      'hide_breadcrumb' => [
        'label'   => 'Hide bredcrumb',
        'description' => 'Hide breadcrumb when this node is displayed',
        'type' => 'boolean',
      ],
      'edge_to_edge' => [
        'label'   => 'Edge to edge',
        'description' => 'Display this node edge to edge',
        'type' => 'boolean',
      ],
      'table_of_content' => [
        'label'   => 'Table of content',
        'description' => 'Generate a table of content block',
        'type' => 'boolean',
      ],
      'custom_theme' => [
        'label'   => 'Select theme',
        'description' => 'Choose from the available themes',
        'type' => 'list_string',
      ],
    ];

    foreach ($fieldnames as $fieldname => $fieldConfig) {
      foreach ($nodeTypesToAdd as $bundle) {
        $this->utilityService->createField('node', $bundle, $fieldname, $fieldConfig);
      }
    }

    $fieldnames = [
      'override_node_settings',
      'hide_sidebars',
      'hide_title',
      'hide_breadcrumb',
      'edge_to_edge',
      'table_of_content',
      'custom_theme',
    ];

    foreach ($fieldnames as $fieldname) {
      foreach ($nodeTypesToRemove as $bundle) {
        $this->utilityService->removeField('node', $bundle, $fieldname);
      }
    }

    $config = \Drupal::configFactory()->getEditable('bootstrap_toolbox.settings');
    $config->set('selectedNodeTypes', $this->selectedNodeTypes)->save();

    $this->messenger()->addStatus($this->t('Done!'));

  }

}
