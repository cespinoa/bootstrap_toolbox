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
      $form_object = $form_state->getFormObject();
      if ($form_object instanceof \Drupal\Core\Entity\EntityFormInterface) {

        $block = $form_object->getEntity();

        if ($block instanceof \Drupal\block\Entity\Block) {
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

      }
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
    /** @var \Drupal\Core\Entity\EntityFormInterface $formObject */
    $formObject = $form_state->getFormObject();
    if ($formObject instanceof \Drupal\Core\Entity\EntityFormInterface) {
      $nodeType = $formObject->getEntity();

      if ($nodeType instanceof \Drupal\node\Entity\NodeType) {
    
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
  $formObject = $formState->getFormObject();
  if ($formObject instanceof \Drupal\Core\Entity\EntityFormInterface){
    $block = $formObject->getEntity();
    if ($block instanceof \Drupal\block\Entity\Block){
      $settings = $formState->getValue('bootstrap_toolbox');
      $block->setThirdPartySetting('bootstrap_toolbox', 'wrapper', $settings['wrapper']);
      $block->setThirdPartySetting('bootstrap_toolbox', 'styles', $settings['styles']);
      $block->save();
    }
  }
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

  if ($wrapper && $style) {
    $build['#prefix'] = '<div class="' . $style . '"><div class="' . $wrapper . '">';
    $build['#suffix'] = '</div></div>';
  } elseif ($wrapper) {
    $build['#prefix'] = '<div class="' . $wrapper . '">';
    $build['#suffix'] = '</div>';
  } elseif ($style) {
    $build['#attributes']['class'][] = $style;
  }

}

/**
 * Implements hook_preprocess_HOOK().
 *
 * Preprocess inline-blocks from Layout Builder.
 */
function bootstrap_toolbox_preprocess_layout(&$variables) {
  if (isset($variables['content']['content'])) {
    foreach ($variables['content']['content'] as $key => $content) {
      if ($key !== '#attributes' && isset($content['#plugin_id']) &&
        strpos($content['#plugin_id'], 'block_content:') === 0) {

        if (isset($content['#bootstrap_toolbox_blockstyle'])) {
          $wrapper = $content['#bootstrap_toolbox_blockstyle']['wrapper'] ?? NULL;
          $styles = $content['#bootstrap_toolbox_blockstyle']['styles'] ?? NULL;

          $utilityService = \Drupal::service('bootstrap_toolbox.utility_service');

          // Procesa el wrapper y el estilo.
          if ($wrapper && $wrapper !== 'none') {
            $wrapper = $utilityService->getWrapperById($wrapper);
          } else {
            $wrapper = NULL;
          }

          if ($styles && $styles !== 'none') {
            $styles = $utilityService->getStyleById($styles);
          } else {
            $styles = NULL;
          }

          // Establecer el prefijo y el sufijo en función de los valores de wrapper y styles.
          if ($wrapper && $styles) {
            $variables['content']['content'][$key]['#prefix'] =
              '<div class="' . $styles . '"><div class="' . $wrapper . '">';
            $variables['content']['content'][$key]['#suffix'] = '</div></div>';
          }
          elseif ($wrapper) {
            $variables['content']['content'][$key]['#prefix'] = '<div class="' . $wrapper . '">';
            $variables['content']['content'][$key]['#suffix'] = '</div>';
          }
          elseif ($styles) {
            $variables['content']['content'][$key]['#attributes']['class'][] = $styles;
          }
        }
      }
    }
  }
}

