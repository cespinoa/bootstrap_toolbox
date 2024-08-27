<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * @todo Add a description for the form.
 */
final class NodesExtraFieldsSettingForm extends ConfirmFormBase {

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
    
    $form['help_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('
          <div class="card">
            <p>Bootstrap Toolbox will add the following fields to the checked node types:</p>
            <ul>
              <li>Hide title</li>
              <li>Hide sidebars</li>
              <li>Hide breadcrumb</li>
              <li>Display edge-to-edge</li>
            </ul>
            <p>Be careful! If you uncheck any node type, their custom fields will be removed and the information contained in them will be lost for this node type.</p>
          </div>
      '),
    ];
    
    $form['selected_node_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select Node Types'),
      '#default_value' => $config->get('selected_node_types') ? $config->get('selected_node_types') : [],
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

    $strings = [
      '@warning' => 'Attention!',
      '@msg_delete_1' => 'Boostrap Toolbox custom fields will be removed from the following node types',
      '@msg_delete_2' => 'This action cannot be undone',
      '@msg_create' => 'Boostrap Toolbox custom fields will be added to the following node types',
    ];
    
    if ($this->node_types_to_remove && count($this->node_types_to_remove)) {
      $deleteText = $this->utilityService->buildDescriptionList($nodeTypes, $this->node_types_to_remove, 'remove');
    } 
    if ($this->node_types_to_add && count($this->node_types_to_add)) {
      $createText = $this->utilityService->buildDescriptionList($nodeTypes, $this->node_types_to_add, 'add');
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

    if(!$this->action){
      $form_state->setRebuild();
      $oldConfig = $this->config('bootstrap_toolbox.settings');
      $oldNodeTypes = $oldConfig->get('selected_node_types') ? $oldConfig->get('selected_node_types') : [];
      $newNodeTypes = $form_state->getValue('selected_node_types')? $form_state->getValue('selected_node_types') : [];
        
      $nodeTypesToAdd = array_diff($newNodeTypes, $oldNodeTypes);
      $nodeTypesToRemove = array_diff($oldNodeTypes, $newNodeTypes);

      $this->node_types_to_add = $this->utilityService->filterNonZeroValues($nodeTypesToAdd);
      $this->node_types_to_remove = $this->utilityService->filterNonZeroValues($nodeTypesToRemove);
      $this->selected_node_types = $newNodeTypes;
      
      $this->action = 'Continue'; 
      return;
    }
    $this->{'process_fields'}();
    
  }
  
  /**
   * Process fields 
   */
  protected function process_fields(): void {
    $nodeTypesToRemove = [];
    $nodeTypesToAdd = [];
    
    if($this->node_types_to_remove && count($this->node_types_to_remove)){
      foreach($this->node_types_to_remove as $key=>$value){
        $nodeTypesToRemove[] = $key;
      }
    } 
    if($this->node_types_to_add && count($this->node_types_to_add)){
      foreach($this->node_types_to_add as $key=>$value){
        $nodeTypesToAdd[] = $key;
      }
    } 
    
    // Add fields starting
    $fieldnames = [
      'override_node_settings' => [
        'label'   => 'Override node settings',
        'description' => 'Override the default configuration',
      ],
      'hide_sidebars' => [
        'label'   => 'Hide sidebars',
        'description' => 'Hide sidebars when this node is displayed',
      ],
      'hide_title' => [
        'label'   => 'Hide title',
        'description' => 'Hide title when this node is displayed',
      ],
      'hide_breadcrumb' => [
        'label'   => 'Hide bredcrumb',
        'description' => 'Hide title when this node is displayed',
      ],
      'edge_to_edge' => [
        'label'   => 'Edge to edge',
        'description' => 'Display this node edge to edge',
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
    ];
    
    foreach ($fieldnames as $fieldname) {
      foreach ($nodeTypesToRemove as $bundle) {
        $this->utilityService->removeField('node', $bundle, $fieldname);
      }
    }   
      
    $config = \Drupal::configFactory()->getEditable('bootstrap_toolbox.settings');
    $config->set('selected_node_types', $this->selected_node_types)->save();
    
    $this->messenger()->addStatus($this->t('Done!'));
    
  }

}
