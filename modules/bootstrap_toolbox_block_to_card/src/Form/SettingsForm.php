<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox_block_to_card\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Bootstrap Toolbox Block to Card settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bootstrap_toolbox_block_to_card_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bootstrap_toolbox_block_to_card.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    
    $introText = '
    The module allows you to add extra classes to the card, the header and the body of the card.<br/>
If you want, you can limit and make it easier for users to enter classes by detailing the default classes in this field.<br/>
If you enter values ​​in these fields, the user will see a selection control with the values ​​you have entered instead of a text field.<br/>
You must enter as many classes as you consider, separated by spaces and put the value none at the beginning in case the user does not want to use any.';
    
    $form['intro_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t($introText),
      '#weight' => -10,
    ];
    
    $form['card_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Card classes'),
      '#default_value' => $this->config('bootstrap_toolbox_block_to_card.settings')->get('card_classes'),
    ];
    
    $form['header_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Header classes'),
      '#default_value' => $this->config('bootstrap_toolbox_block_to_card.settings')->get('header_classes'),
    ];
    
    $form['body_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Card classes'),
      '#default_value' => $this->config('bootstrap_toolbox_block_to_card.settings')->get('body_classes'),
    ];
    
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('bootstrap_toolbox_block_to_card.settings')
      ->set('card_classes', $form_state->getValue('card_classes'))
      ->set('header_classes', $form_state->getValue('header_classes'))
      ->set('body_classes', $form_state->getValue('body_classes'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
