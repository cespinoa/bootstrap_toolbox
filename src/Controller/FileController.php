<?php

namespace Drupal\bootstrap_toolbox\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\Core\File\FileSystemInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Messenger\MessengerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to handle file creation and redirection.
 */
class FileController extends ControllerBase {

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
   * Constructs a YourController object.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(FileSystemInterface $file_system, LoggerInterface $logger, MessengerInterface $messenger) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('logger.factory')->get('bootstrap_toolbox'),
      $container->get('messenger')
    );
  }

  /**
   * Create the directory and file, then redirect.
   */
  public function createFiles() {
    
    $publicPath = $this->fileSystem->realpath('public://');
    $directoryPath = $publicPath . '/bootstrap_toolbox';
    $filePath = $directoryPath . '/custom.css';
    $comment = "#~ ===================================================\n";
    $comment .= "#~ Notice\n";
    $comment .= "#~ You can edit it via the Bootstrap Toolbox settings\n";
    $comment .= "#~ ===================================================\n\n";


    if ($this->fileSystem->prepareDirectory($directoryPath, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY)){
      if (file_put_contents($filePath, $comment) === FALSE) {
        $msg = $this->t('Failed to create custom css file.');
        $this->logger->error($msg);
        $this->messenger->addError($msg);
      } else {
        $msg = $this->t('Css file creted successfully.');
        $this->logger->notice($msg);
        $this->messenger->addStatus($msg);
        
      }
    }
    else {
      $msg = $this->t('Failed to create directory @directoryPath in @publicPath',
        [
          '@directoryPath' => $directoryPath,
          '@publicPath' => $publicPath,
        ]);
      $this->logger->error($msg);
      $this->messenger->addError($msg);
    }

    return new RedirectResponse('/admin/config/bootstrap-toolbox#custom_css');
  }

}
