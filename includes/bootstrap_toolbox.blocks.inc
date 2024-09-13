<?php

/**
 * @file
 * Hook implementations for the bootstrap_toolbox module.
 *
 * Contains blocks related functions.
 */

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 *
 * Add ThirdPartySettings to blocks: wrapper and extra_class.
 */
function bootstrap_toolbox_form_alter(
  &$form,
  FormStateInterface $form_state,
  $form_id,
) {
  $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
  if ($form_id == 'block_form'
    || $form['#form_id'] === 'layout_builder_add_block'
    || $form['#form_id'] === 'layout_builder_update_block') {
    
    $wrapperOptions = $utilityService
      ->getWrapperList();
    $stylesOptions = $utilityService
      ->getScopeListFiltered(['blocks_formatters']);

    if ($form_id == 'block_form') {
      $block = $form_state->getFormObject()->getEntity();
      $config = \Drupal::config('bootstrap_toolbox.settings');

      $form['bootstrap_toolbox_verticaltabs'] = [
        '#type' => 'vertical_tabs',
        '#title' => t('Bootstrap Toolbox Settings'),
        '#weight' => 0,
      ];

      $form['bootstrap_toolbox'] = [
        '#type' => 'details',
        '#title' => t('Wrapper and classes'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        '#group' => 'bootstrap_toolbox_verticaltabs',
      ];

      $form['bootstrap_toolbox']['wrapper'] = [
        '#type' => 'select',
        '#title' => t('Bootstrap Toolbox Extra Wrapper'),
        '#default_value' => $block
          ->getThirdPartySetting('bootstrap_toolbox', 'wrapper'),
        '#empty_option' => t('None'),
        '#options' => $wrapperOptions,
      ];

      $form['bootstrap_toolbox']['styles'] = [
        '#type' => 'select',
        '#title' => t('Bootstrap Toolbox Extra Classes'),
        '#default_value' => $block
          ->getThirdPartySetting('bootstrap_toolbox', 'styles'),
        '#options' => $stylesOptions,
        '#empty_option' => t('None'),
      ];

      $form['actions']['submit']['#submit'][] = 'bootstrap_toolbox_form_alter_submit';

    }
    elseif ($form_id === 'layout_builder_add_block' ||
      $form_id === 'layout_builder_update_block') {
      /** @var \Drupal\layout_builder\Form\ConfigureBlockFormBase $formObject */
      $formObject = $form_state->getFormObject();

      $component = $formObject->getCurrentComponent();
      $blockPluginId = $component->getPluginId();

      // If this is a reusable block, retrieve the block bundle.
      if (strpos($blockPluginId, 'block_content:') === 0) {
        $settings = $component->get('bootstrap_toolbox_blockstyle');

        $form['bootstrap_toolbox'] = [
          '#type' => 'details',
          '#title' => 'Bootstrap Toolbox',
          '#weight' => 0,
        ];

        $form['bootstrap_toolbox']['wrapper'] = [
          '#type' => 'select',
          '#title' => ('Extra wrapper'),
          '#default_value' => $settings['wrapper'] ?? 'none',
          '#options' => $wrapperOptions,
          '#empty_option' => t('None'),
        ];
        $form['bootstrap_toolbox']['styles'] = [
          '#type' => 'select',
          '#title' => ('Extra classes'),
          '#default_value' => $settings['styles'] ?? 'none',
          '#options' => $stylesOptions,
          '#empty_option' => t('None'),
        ];
        array_unshift($form['#submit'], 'bootstrap_toolbox_submit_block_form');
      }
    }

  }
  elseif ($form_id === 'node_type_edit_form' || $form_id === 'node_type_add_form') {
    /** @var \Drupal\node\Entity\NodeType $nodeType */
    $nodeType = $form_state
      ->getFormObject()->getEntity();
    $hideTitle = $nodeType
      -> getThirdPartySetting('bootstrap_toolbox', 'hide_title', FALSE);
    $hideSidebars = $nodeType
      ->getThirdPartySetting('bootstrap_toolbox', 'hide_sidebars', FALSE);
    $hideBreadcrumb = $nodeType
      ->getThirdPartySetting('bootstrap_toolbox', 'hide_breadcrumb', FALSE);
    $edgeToEdge = $nodeType
      ->getThirdPartySetting('bootstrap_toolbox', 'edge_to_edge', FALSE);
    $customTheme = $nodeType
      ->getThirdPartySetting('bootstrap_toolbox', 'custom_theme', FALSE);
    
    $form['bootstrap_toolbox'] = [
      '#type' => 'details',
      '#title' => t('Bootstrap Toolbox Settings'),
      '#group' => 'additional_settings',
    ];

    $form['bootstrap_toolbox']['hide_title'] = [
      '#type' => 'checkbox',
      '#title' => t('Hide title'),
      '#default_value' => $hideTitle,
    ];

    $form['bootstrap_toolbox']['hide_sidebars'] = [
      '#type' => 'checkbox',
      '#title' => t('Hide sidebars'),
      '#default_value' => $hideSidebars,
    ];

    $form['bootstrap_toolbox']['hide_breadcrumb'] = [
      '#type' => 'checkbox',
      '#title' => t('Hide breadcrumbs'),
      '#default_value' => $hideBreadcrumb,
    ];

    $form['bootstrap_toolbox']['edge_to_edge'] = [
      '#type' => 'checkbox',
      '#title' => t('Display edge to edge'),
      '#default_value' => $edgeToEdge,
    ];

    $form['bootstrap_toolbox']['custom_theme'] = [
      '#type' => 'select',
      '#title' => t('Custom theme'),
      '#default_value' => $customTheme,
      '#options' => $utilityService->getAllowedThemes(),
      '#empty_option' => t('None'),
      '#weight' => 90,
    ];

    $form['#entity_builders'][] = 'bootstrap_toolbox_form_node_type_edit_form_builder';

  }
}

/**
 * Custom submit handler for submitting LB block forms.
 *
 * Persists the configured block style to the component configuration data,
 * which is later persisted to section storage by layout builder's base form.
 */
function bootstrap_toolbox_submit_block_form(array $form, FormStateInterface $form_state) {
  /** @var \Drupal\layout_builder\Form\ConfigureBlockFormBase $formObject */
  $formObject = $form_state->getFormObject();
  $component = $formObject->getCurrentComponent();
  $settings = $form_state->getValue('bootstrap_toolbox');
  $newSettings = $component->get('bootstrap_toolbox_blockstyle');
  $newSettings['wrapper'] = $settings['wrapper'];
  $newSettings['styles'] = $settings['styles'];
  $component->set('bootstrap_toolbox_blockstyle', $newSettings);
}

/**
 * Implements hook_block_submit_alter().
 */
function bootstrap_toolbox_form_alter_submit($form, FormStateInterface $formState) {
  $block = $formState->getFormObject()->getEntity();
  // ['bootstrap_toolbox'];
  $settings = $formState->getValue('bootstrap_toolbox');
  $block->setThirdPartySetting('bootstrap_toolbox', 'wrapper', $settings['wrapper']);
  $block->setThirdPartySetting('bootstrap_toolbox', 'styles', $settings['styles']);
  $block->save();
  // ~ $text = $settings['wrapper'];
}

/**
 * Implements hook_block_view_alter().
 */
function bootstrap_toolbox_block_view_alter(array &$build, BlockPluginInterface $block) {
  $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
  $entityBlock = $build['#block'];
  $wrapper = $entityBlock->getThirdPartySetting('bootstrap_toolbox', 'wrapper');
  $style = $entityBlock->getThirdPartySetting('bootstrap_toolbox', 'styles');

  if ($wrapper && $wrapper != 'none') {
    $wrapper = $utilityService->getWrapperById($wrapper);
  }

  if ($style && $style != 'none') {
    $style = $utilityService->getStyleById($style);
  }

  if ($wrapper && !$style) {
    $build['#prefix'] = '<div class="' . $wrapper . '">';
    $build['#suffix'] = '</div>';
  }
  elseif ($wrapper && $style) {
    $build['#prefix'] = '<div class="' . $style . '"><div class="' . $wrapper . '">';
    $build['#suffix'] = '</div></div>';
  }
  elseif (!$wrapper && $style) {
    $build['#attributes'] = ['class' => [$style]];
  }

}

/**
 * Implements hook_preprocess_HOOK().
 *
 * Preprocess inline-blocks from Layout Builder.
 */
function bootstrap_toolbox_preprocess_layout(&$variables) {
  if (array_key_exists('content', $variables['content'])) {
    foreach ($variables['content']['content'] as $key => $content) {
      if ($key != '#attributes') {
        if (array_key_exists('#plugin_id', $content) &&
          strpos($content['#plugin_id'], 'block_content:') === 0) {
          if (array_key_exists('#bootstrap_toolbox_blockstyle', $content)) {
            $wrapper = $content['#bootstrap_toolbox_blockstyle']['wrapper'];
            $styles = $content['#bootstrap_toolbox_blockstyle']['styles'];

            $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');
            if ($wrapper && $wrapper != 'none') {
              $wrapper = $utilityService->getWrapperById($wrapper);
            }

            if ($styles && $styles != 'none') {
              $styles = $utilityService->getStyleById($styles);
            }
            if ($wrapper && !$styles) {
              $variables['content']['content'][$key]['#prefix'] = '<div class="' . $wrapper . '">';
              $variables['content']['content'][$key]['#suffix'] = '</div>';
            }
            elseif ($wrapper && $styles) {
              $variables['content']['content'][$key]['#prefix'] =
                '<div class="' . $styles . '"><div class="' . $wrapper . '">';
              $variables['content']['content'][$key]['#suffix'] = '</div></div>';
            }
            elseif (!$wrapper && $styles) {
              $variables['content']['content'][$key]['#attributes'] = ['class' => [$styles]];
            }

          }
        }
      }
    }
  }

}
