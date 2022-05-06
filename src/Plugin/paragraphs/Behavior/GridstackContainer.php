<?php

namespace Drupal\paragraphs_gridstack\Plugin\paragraphs\Behavior;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphsBehaviorBase;
use Drupal\paragraphs\Annotation\ParagraphsBehavior;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;

/**
 * Gridstack container behavior implementation.
 *
 * @ParagraphsBehavior(
 *   id = "gridstack_container",
 *   label = @Translation("Gridstack Container"),
 *   description = @Translation("Provides Gridstack layouts."),
 *   weight = 0,
 * )
 */
class GridstackContainer extends ParagraphsBehaviorBase {

  /**
   * {@inheritdoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode) {
    // @TODO Move a library attachment in this methods.
  }

  /**
   * {@inheritdoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state) {
    // @TODO Fetch from Gridstack optionset.
    $config = \Drupal::config('tau_gridstack.settings')->get('config');


    $form['grid_columns'] = [
      '#type' => 'select',
      '#options' => [
        // @TODO Fetch from optionset.
        '12' => $this->t('Small Grid'),
        '56' => $this->t('Advanced Grid'),
      ],
      '#attributes' => [
        'class' => ['grid_columns'],
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_columns', '56'),
    ];

    $form['grid_json_template'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['grid_json_template'],
      ],
      // @TODO Fetch from optionset.
      '#default_value' => $config['template_desktop'] ?? '',
    ];

    $form['grid_json_default'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['grid_json_default'],
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_default', ''),
    ];

    $form['grid_json'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['grid_json'],
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json', ''),
    ];

    $form['grid_columns_mobile'] = [
      '#type' => 'select',
      '#options' => [
        // @TODO Fetch from optionset.
        '7' => $this->t('Small Grid'),
        '12' => $this->t('Advanced Grid'),
      ],
      '#attributes' => [
        'class' => ['grid_columns_mobile', '--hide'],
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_columns_mobile', '12'),
    ];

    $form['grid_json_mobile_template'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['grid_json_mobile_template'],
      ],
      // @TODO Fetch from optionset.
      '#default_value' => $config['template_mobile'] ?? '',
    ];

    $form['grid_json_mobile_default'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['grid_json_mobile_default'],
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_mobile_default', ''),
    ];

    $form['grid_json_mobile'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['grid_json_mobile'],
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_mobile', ''),
    ];

    // @TODO Why attachment to the first field?
    $form['grid_json']['#attached']['library'][] = 'tau_gridstack/paragraphs_gridstack';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(Paragraph $paragraph) {
    // @TODO Looks like fucking bullshit, need to rewrite.
    $grid_columns = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_columns');
    $grid_json_template = $paragraph->getBehaviorSetting($this->getPluginId(), 'template_desktop');
    $grid_json_default = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_default');
    $grid_json = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json');
    $grid_columns_mobile = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_columns_mobile');
    $grid_json_mobile_template = $paragraph->getBehaviorSetting($this->getPluginId(), 'template_mobile');
    $grid_json_mobile_default = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_mobile_default');
    $grid_json_mobile = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_mobile');
    return [
      $grid_columns ? $this->t('Columns: @element', ['@element' => $grid_columns]) : '56',
      $grid_json_template ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_template]) : '',
      $grid_json_default ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_default]) : '',
      $grid_json ? $this->t('GridStack JSON: @element', ['@element' => $grid_json]) : '',
      $grid_columns_mobile ? $this->t('Columns: @element', ['@element' => $grid_columns_mobile]) : '12',
      $grid_json_mobile_template ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_mobile_template]) : '',
      $grid_json_mobile_default ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_mobile_default]) : '',
      $grid_json_mobile ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_mobile]) : '',
    ];
  }

}
