<?php
namespace Drupal\bootstrap_toolbox\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ExtensionDiscovery;

class BootstrapToolboxSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bootstrap_toolbox.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bootstrap_toolbox_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('bootstrap_toolbox.settings');

    // Get all extensions available in the system.
    $extension_discovery = new ExtensionDiscovery(\Drupal::root());
    $extensions = $extension_discovery->scan('module') + $extension_discovery->scan('theme');
  
    $library_options = [];
    foreach ($extensions as $extension => $extension_info) {
      $libraries = \Drupal::service('library.discovery')->getLibrariesByExtension($extension);
      foreach ($libraries as $lib_name => $lib) {
        if ($this->isBootstrapLibrary($lib)) {
          $library_options["$extension/$lib_name"] = "$extension/$lib_name";
        }
      }
    }

    $form['selected_library'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a library'),
      '#default_value' => $config->get('selected_library'),
      '#options' => $library_options,
      '#description' => $this->t('Select which library to use for the Bootstrap Toolbox.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Check if the library contains bootstrap related files.
   *
   * @param array $library
   *   The library definition array.
   *
   * @return bool
   *   TRUE if the library contains bootstrap related files, FALSE otherwise.
   */
  protected function isBootstrapLibrary(array $library) {
    
    foreach (['css', 'js'] as $type) {
      if (!empty($library[$type])) {
        foreach ($library[$type] as $files) {
          foreach ($files as $file => $attributes) {
            if (is_string($attributes) && strpos($attributes, 'bootstrap') !== FALSE) {
              $attributes = explode('/',$attributes)[1];
              if (strpos($attributes, 'bootstrap') !== FALSE){
                return TRUE;
              }
            }
          }
        }
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('bootstrap_toolbox.settings')
      ->set('selected_library', $form_state->getValue('selected_library'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
