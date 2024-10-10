<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Bootstrap Toolbox settings for this site.
 */
final class BaseThemesForm extends FormBase {

  


  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected UtilityServiceInterface $utilityService;

  /**
   * {@inheritdoc}
   */
  public function __construct(UtilityServiceInterface $utilityService) {
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bootstrap_toolbox.utility_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bootstrap_toolbox_base_themes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config('bootstrap_toolbox.settings');

    $routeMatch = $this->utilityService->getRouteMatch();
    if ($routeMatch instanceof \Drupal\Core\Routing\RouteMatchInterface) {
      $action = $routeMatch->getParameter('action');
      $themeId = $routeMatch->getParameter('themeId');
    }
    else {
      $action = 'create';
      $themeId = '';
    }
    
    

    $themeName = '';
    $bootstrapVersion = 5;
    $sidebarsVariables = 'sidebar_first sidebar_second';
    $mainAreaSelector = '#main';
    $mainAreaClass = 'container-fluid';
    $centralPanelClass = 'container';
    $tocSelector = '.aside';
    $stickyClass = 'sticky-top';
    
        
    if (($action == 'edit' || $action == 'copy' || $action == 'delete') && $themeId){
      $customBasethemes = $this->utilityService->getCustomBasethemes();
      if (is_array($customBasethemes) && isset($customBasethemes[$themeId])) {
          $config = $customBasethemes[$themeId];
      } else {
          $this->messenger()->addError($this->t('Invalid theme ID or base themes not found.'));
          $config = null;  
      }
      if ($config) {
        $themeName = $action == 'edit' ? $config['name'] : $config['name'] . ' - copy ' ;
        $bootstrapVersion = $config['bootstrap_version'];
        $sidebarsVariables = implode(' ', $config['sidebars_variables']);
        $mainAreaSelector = $config['main_area_selector'];
        $mainAreaClass = $config['main_area_class'];
        $centralPanelClass = $config['central_panel_class'];
        $tocSelector =  $config['toc_selector'];
        $stickyClass =  $config['sticky_class'];
      }
    }

    $form_state->set('themeId', $themeId);
    $form_state->set('themeName', $themeName);

    if ( $action == 'delete' ){
      $form['message'] = [
        '#type' => 'markup',
        '#markup' => $this->t('You are about to delete the theme @theme. This action cannot be undone.', ['@theme' => $themeName]),
        '#prefix' => '<div class="messages messages--warning">',
        '#suffix' => '</div>',
      ];
    }
    else {
      $form['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Base theme name'),
        '#default_value' => $themeName,
        '#required' => TRUE,
      ];
      
      $form['bootstrap_version'] = [
        '#type' => 'select',
        '#title' => $this->t('Bootstrap version'),
        '#options' => [
          3 => 'V.3',
          4 => 'V.4',
          5 => 'V.5',
        ],
        '#default_value' => $bootstrapVersion,
      ];

      $form['sidebars_variables'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Sidebars classes'),
        '#default_value' => $sidebarsVariables,
        '#required' => TRUE,
        '#description' => $this->t('The names of sidebars variables.<br/> You can find out their names by looking in the theme\'s info file.<br/>Write their names separating them with a space'),
      ];

      $form['main_area_selector'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Main area selector'),
        '#default_value' => $mainAreaSelector,
        '#required' => TRUE,
        '#description' => $this->t('The main area selector.<br/>If it is an id you must write it preceded by the # sign, if it is a class you must start it with a period. For example: #main or .main-area)<br/>You can see its value by exploring the html of the page'),
      ];

      $form['main_area_class'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Fluid main area class'),
        '#default_value' => $mainAreaClass,
        '#required' => TRUE,
        '#description' => $this->t('The main area class in fluid mode.'),
      ];

      $form['central_panel_class'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Central panel class'),
        '#default_value' => $centralPanelClass,
        '#required' => TRUE,
        '#description' => $this->t('The central panel class.'),
      ];

      $form['toc_selector'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Selector of Table of content'),
        '#default_value' => $tocSelector,
        '#required' => TRUE,
        '#description' => $this->t('The area to be marked as sticky'),
      ];

      $form['sticky_class'] = [
        '#type' => 'textfield',
        '#title' => $this->t('The class to be applied to area of toc selector'),
        '#default_value' => $stickyClass,
        '#required' => TRUE,
        '#description' => $this->t('The area to be marked as sticky'),
      ];
    }



    $form['create'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create'),
      '#submit' => ['::submitCreate'],
      '#access' => ($action === 'create'),
    ];

    $form['edit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Edit'),
      '#submit' => ['::submitEdit'],
      '#access' => ($action === 'edit'),
    ];

    $form['copy'] = [
      '#type' => 'submit',
      '#value' => $this->t('Copy'),
      '#submit' => ['::submitCopy'],
      '#access' => ($action === 'copy'),
    ];

    $form['delete'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#submit' => ['::submitDelete'],
      '#access' => ($action === 'delete'),
    ];

    
    return $form;
  }

  public function submitCreate(array &$form, FormStateInterface $form_state) {
    $themeName = $form_state->getValue('name');
    $themeId = $this->utilityService->getClassName($themeName);
    $this->processSubmit('Created', $themeId, $form_state); 
  }

  public function submitEdit(array &$form, FormStateInterface $form_state) {
    $themeId = $form_state->get('themeId');
    $this->processSubmit('Edited', $themeId, $form_state); 
  }

  public function submitCopy(array &$form, FormStateInterface $form_state) {
    $themeName = $form_state->getValue('name');
    $themeId = $this->utilityService->getClassName($themeName);
    $this->processSubmit('Copied', $themeId, $form_state); 
  }

  public function submitDelete(array &$form, FormStateInterface $form_state) {
    $themeId = $form_state->get('themeId');
    $themeName = $form_state->get('themeName');
    $config = $this->utilityService->getCustomBasethemes() ?? [];
    unset($config[$themeId]);
    $result = $this->utilityService->saveCustomBasethemes($config);

    if ($result){
      $message = $this->t('The theme @theme has been removed.', ['@theme' => $themeName]);
      $this->utilityService->displayMessage('addMessage',$message);
    }
    else {
      $message = $this->t('Failed to write to YAML file');
      $this->utilityService->displayMessage('addError',$message);
    }

    $form_state->setRedirect('bootstrap_toolbox.settings', [], ['fragment' => 'base_theme_list']);
  }

  public function processSubmit($action, $themeId, $form_state){
    $config = $this->utilityService->getCustomBasethemes();
    $config[$themeId] = [
      'name' => $form_state->getValue('name'),
      'sidebars_variables' => explode(' ',$form_state->getValue('sidebars_variables')),
      'main_area_selector' => $form_state->getValue('main_area_selector'),
      'main_area_class' => $form_state->getValue('main_area_class'),
      'bootstrap_version' => $form_state->getValue('bootstrap_version'),
      'central_panel_class' => $form_state->getValue('central_panel_class'),
      'toc_selector' => $form_state->getValue('toc_selector'),
      'sticky_class' => $form_state->getValue('sticky_class'),
    ];
    $themeName = $form_state->getValue('name');
    
    $result = $this->utilityService->saveCustomBasethemes($config);

    if ($result){
      $message = $this->t('@action theme: @theme', ['@action' => $action, '@theme' => $themeName]);
      $this->utilityService->displayMessage('addMessage',$message);
    }
    else {
      $message = $this->t('Failed to write to YAML file');
      $this->utilityService->displayMessage('addError',$message);
    }

    $form_state->setRedirect('bootstrap_toolbox.settings', [], ['fragment' => 'base_theme_list']);

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }
  

  

}
