<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox_list_group\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * List Group with Panels style plugin.
 *
 * @ViewsStyle(
 *   id = "bootstrap_toolbox_list_group_list_group_with_panels",
 *   title = @Translation("List Group with Panels"),
 *   help = @Translation("@todo Add help text here."),
 *   theme = "views_style_bootstrap_toolbox_list_group_list_group_with_panels",
 *   display_types = {"normal"},
 * )
 */
final class BootstrapToolboxListGroupWithPanels extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /**
   * {@inheritdoc}
   */
  protected $usesRowClass = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['wrapper_class'] = ['default' => 'item-list'];
    $options['view_fields'] = ['default' => ''];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $formstate): void {
    parent::buildOptionsForm($form, $formstate);
    
    //~ $this->usesFields();
    
    
    
    $fields = [];
    $fieldDefinitions = $this->view->display_handler->handlers['field'];
    if (!empty($fieldDefinitions)) {
      foreach ($fieldDefinitions as $fieldname => $fieldDefinition) {
        
        $title = $fieldDefinition->configuration['title'];
        if(is_object($title)){
          $title = $title->__toString();
        }
        $fields[$fieldname] = $title;
        
      }
    }
    

    $form['item_list'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the list element'),
      '#options' => $fields,
      '#default_value' => $this->options['item_list'],
      '#description' => $this->t('Select a field from the view.'),
    ];
    
    $form['selected_panel'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the panel'),
      '#options' => $fields,
      '#default_value' => $this->options['selected_panel'],
      '#description' => $this->t('Select a field from the view.'),
    ];
    
    
    
  }

}
