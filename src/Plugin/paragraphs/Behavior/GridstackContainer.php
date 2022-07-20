<?php

namespace Drupal\paragraphs_gridstack\Plugin\paragraphs\Behavior;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Link;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\paragraphs\Annotation\ParagraphsBehavior;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\paragraphs_gridstack\ParagraphsGridstackManagerInterface;
use Drupal\paragraphs_gridstack\GridstackBreakpointsManagerInterface;

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
class GridstackContainer extends ParagraphsBehaviorBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * ParagraphsGridstackManagerInterface definition.
   *
   * @var \Drupal\paragraphs_gridstack\ParagraphsGridstackManagerInterface
   */
  protected ParagraphsGridstackManagerInterface $gridstackOptionsetsManager;

  /**
   * GridstackBreakpointsManagerInterface definition.
   *
   * @var \Drupal\paragraphs_gridstack\GridstackBreakpointsManagerInterface
   */
  protected GridstackBreakpointsManagerInterface $gridstackBreakpointsManager;

  /**
   * LibraryDiscoveryInterface definition.
   *
   * @var \Drupal\Core\Asset\LibraryDiscoveryInterface
   */
  protected LibraryDiscoveryInterface $libraryDiscovery;

  /**
   * GridstackContainer plugin constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManagerInterface $entity_field_manager, ConfigFactoryInterface $container_factory, ParagraphsGridstackManagerInterface $gridstack_optionsets_manager, GridstackBreakpointsManagerInterface $gridstack_breakpoints_manager, LibraryDiscoveryInterface $library_discovery) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_field_manager);
    $this->configFactory = $container_factory;
    $this->gridstackOptionsetsManager = $gridstack_optionsets_manager;
    $this->gridstackBreakpointsManager = $gridstack_breakpoints_manager;
    $this->libraryDiscovery = $library_discovery;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager'),
      $container->get('config.factory'),
      $container->get('paragraphs_gridstack.manager'),
      $container->get('paragraphs_gridstack.breakpoints_manager'),
      $container->get('library.discovery'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode) {
    //$build['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack';
    // @TODO Pass gridstack data into the paragraph attributes.
  }

  /**
   * {@inheritdoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state) {
    $form_wrapper_id = Html::getUniqueId('paragraphs-gridstack-wrapper');

    $form['container'] = [
      '#attributes' => [
        'class' => ['paragraphs-gridstack-wrapper'],
      ],
      '#attached' => [
        'library' => ['paragraphs_gridstack/paragraphs_gridstack.base'],
      ],
      '#prefix' => '<div id="' . $form_wrapper_id . '">',
      '#suffix' => '</div>',
    ];

    $optionsets = $this->buildOptionsetsOptions();
    $description = $this->t('You can manage Gridstack Optionsets here.');
    $management_page_route = 'entity.paragraphs_gridstack.list';

    $selected_optionset = $paragraph->getBehaviorSetting(
      $this->getPluginId(),
      'optionset',
      'paragraphs_gridstack.optionset.default'
    );

    $form['container']['optionset'] = [
      '#type' => 'select',
      '#options' => $optionsets,
      '#default_value' => $selected_optionset,
      '#title' => $this->t('Choose the Gridstack Optionset:'),
      '#description' => Link::createFromRoute($description, $management_page_route)->toRenderable(),
      '#ajax' => [
        'event' => 'change',
        'wrapper' => $form_wrapper_id,
        'progress' => ['type' => 'fullscreen'],
        'callback' => [$this, 'ajaxRebuildProcess'],
      ],
    ];

    $form['container']['buttons'] = ['#type' => 'container'];
    $form['container']['buttons']['set_by_template'] = [
      '#type' => 'button',
      '#title' => $this->t('Set by template'),
      '#attributes' => [
        'id' => 'pg-action-set-by-template',
        'class' => ['paragraphs-gridstack-action'],
        'title' => $this->t('Press to set elements like in template. You can manage template settings in the Gridstack Optionset.'),
      ],
    ];

    $form['container']['buttons']['restore'] = [
      '#type' => 'button',
      '#title' => $this->t('Restore settings'),
      '#attributes' => [
        'id' => 'pg-action-restore',
        'class' => ['paragraphs-gridstack-action'],
        'title' => $this->t('Press to restore settings of the latest revision.'),
      ],
    ];

    // Get selected optionset from form state.
    if ($optionset = $form_state->getValue('optionset')) {
      $selected_optionset = $optionset;
    }

    /** @var \Drupal\paragraphs_gridstack\Entity\ParagraphsGridstack $optionset_entity */
    $optionset_entity = $this->gridstackOptionsetsManager->get($selected_optionset);
    $breakpoints_provider = $optionset_entity->get('breakpoints_provider');
    /** @var \Drupal\breakpoint\Breakpoint[] $breakpoints */
    $breakpoints = $this->gridstackBreakpointsManager->getBreakpointsByCondition($breakpoints_provider);

    // Get list of the available columns.
    $columns_options = $this->buildColumnsOptions();
    // Template settings of the optionset.
    $template_default = $optionset_entity->get('template');

    $mapping = [
      'columns' => [
        'type' => 'select',
        'options' => $columns_options,
        'default' => NULL,
      ],
      'template' => [
        'type' => 'textarea',
        'default' => $template_default ?? '{}',
      ],
      'previous' => [
        'type' => 'textarea',
        'default' => '{}',
      ],
      'storage' => [
        'type' => 'textarea',
        'default' => '{}',
      ],
    ];

    foreach ($mapping as $setting_key => $settings) {
      foreach ($breakpoints as $breakpoint) {
        $machine_name = "{$breakpoint->getPluginId()}.$setting_key";

        $default_value = $paragraph
          ->getBehaviorSetting(
            $this->getPluginId(),
            $machine_name,
            $settings['default']
          );

        $form['container']['settings'][$breakpoint->getPluginId()][$machine_name] = [
          '#type' => $settings['type'],
          '#default_value' => $default_value,
          '#attributes' => ['class' => [$machine_name]],
        ];

        if (!empty($settings['options'])) {
          $form['container']['settings'][$breakpoint->getPluginId()][$machine_name]['#options'] = $settings['options'];
        }
      }
    }

    return $form;
  }

  /**
   * Ajax callback to rebuild behaviour form.
   *
   * @param array $form
   *   Render array of behaviour form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state object of the behaviour form.
   *
   * @return array
   *   Render array of the behaviour form.
   */
  public function ajaxRebuildProcess(array &$form, FormStateInterface $form_state): array {
    // Rebuild FormState and return form.
    $form_state->setRebuild();
    // @todo Fix problem with broken form here.
    //return $form['field_gridstack_paragraphs']['widget'][0]['behavior_plugins']['gridstack_container']['container'];
    return $form['field_gridstack_paragraphs']['widget'][0];
  }

  /**
   * Return array of gridstack options sets.
   *
   * @return array
   *   Array of options.
   */
  public function buildOptionsetsOptions() {
    $options = [];
    $optionsets = $this->gridstackOptionsetsManager->listAll();

    foreach ($optionsets as $optionset) {
      $config = $this->configFactory->get($optionset);
      $options[$optionset] = $config->get('label');
    }

    return $options;
  }

  /**
   * Return array of available columns.
   *
   * @return array
   *   Array of options.
   */
  public function buildColumnsOptions() {
    $options = [];
    $libraries = $this->libraryDiscovery
      ->getLibrariesByExtension('paragraphs_gridstack');

    foreach ($libraries as $machine_name => $properties) {
      if (strpos($machine_name, 'paragraphs_gridstack.columns.') !== FALSE) {
        $options[$machine_name] = $properties['label'];
      }
    }

    return $options;
  }

}
