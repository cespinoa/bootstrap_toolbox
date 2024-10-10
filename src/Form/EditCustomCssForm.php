<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\File\FileSystemInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Bootstrap Toolbox form.
 */
final class EditCustomCssForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bootstrap_toolbox_edit_custom_css';
  }

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Constructs a YourController object.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   */
  public function __construct(FileSystemInterface $file_system,
  LoggerInterface $logger,
  MessengerInterface $messenger,
  ModuleHandlerInterface $moduleHandler
  ) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
    $this->messenger = $messenger;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('logger.factory')->get('bootstrap_toolbox'),
      $container->get('messenger'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $publicPath = $this->fileSystem->realpath('public://');
    $filePath = $publicPath . '/bootstrap_toolbox/custom.css';
    $content = file_get_contents($filePath);
    
    if($content){
      $form['content'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Message'),
        '#required' => TRUE,
        '#default_value' => $content,
        '#rows' => 20,
      ];
      
      if ($this->moduleHandler->moduleExists('codemirror_editor')){
        $form['content']['#codemirror'] = [
          'mode' => 'css',
        ];
      }

      $form['actions'] = [
        '#type' => 'actions',
        'submit' => [
          '#type' => 'submit',
          '#value' => $this->t('Save'),
        ],
      ];      
    }
    else {
      $form['message'] = [
        '#markup' => $this->t('The file @filePath could not be opened', ['@filePath' => $filePath]),
        '#prefix' => '<div class="messages messages--warning">',
        '#suffix' => '</div>',
      ];
      $form['action'] = [
        '#type' => 'link',
        '#title' => $this->t('Close'),
        '#url' => \Drupal\Core\Url::fromRoute('bootstrap_toolbox.settings', [], ['fragment' => 'custom_css']),
        '#attributes' => [
          'class' => ['button', 'button--primary'], 
        ],
      ];
    }



    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $publicPath = $this->fileSystem->realpath('public://');
    $filePath = $publicPath . '/bootstrap_toolbox/custom.css';
    $content = $form_state->getValue('content');
    if (file_put_contents($filePath, $content) === FALSE) {
      $msg = $this->t('Failed to save custom css.');
      $this->logger->error($msg);
      $this->messenger->addError($msg);
    }
    else {
      $msg = $this->t('Custom css saved successfully.');
      $this->logger->notice($msg);
      $this->messenger->addStatus($msg);
    }
    $form_state->setRedirect('bootstrap_toolbox.settings', [], ['fragment' => 'custom_css']);
    
  }

}
