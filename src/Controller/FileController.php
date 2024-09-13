<?php

namespace Drupal\bootstrap_toolbox\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller to handle file creation and redirection.
 */
class FileController extends ControllerBase {

  /**
   * Create the directory and file, then redirect.
   */
  public function createFiles() {
    // Get the file system service.
    $file_system = \Drupal::service('file_system');

    // Define the directory and file path.
    $public_path = $file_system->realpath('public://');
    $directory_path = $public_path . '/bootstrap_toolbox';
    $file_path = $directory_path . '/custom.css';

    // Create the directory if it does not exist.
    if (!file_exists($directory_path)) {
      mkdir($directory_path, 0777, TRUE);
    }

    // Create the file if it does not exist.
    if (!file_exists($file_path)) {
      $file_content = "/* Custom CSS file */";
      file_put_contents($file_path, $file_content);
    }

    // Redirect to the settings page.
    return new RedirectResponse('/admin/config/bootstrap-toolbox');
  }

}
