<?php

namespace Drupal\paragraphs_gridstack\Plugin\paragraphs\Behavior;

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
    $build['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack';
    // @TODO Pass gridstack data into the paragraph attributes.
  }

  /**
   * {@inheritdoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state) {
    // @TODO Fetch from Gridstack optionset.
    // $config = \Drupal::config('tau_gridstack.settings')->get('config');

    $options = $this->buildOptions();
    $description = $this->t('You can manage Gridstack Optionsets here.');
    $management_page_route = 'entity.paragraphs_gridstack.list';

    $form['optionset'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'optionset', 'paragraphs_gridstack.optionset.default'),
      '#title' => $this->t('Choose the Gridstack Optionset:'),
      '#description' => Link::createFromRoute($description, $management_page_route)->toRenderable(),
    ];

    $form['buttons'] = ['#type' => 'container'];
    $form['buttons']['set_by_template'] = [
      '#type' => 'button',
      '#title' => $this->t('Set by template'),
      '#attributes' => [
        'id' => 'pg-action-set-by-template',
        'class' => ['paragraphs-gridstack-action'],
        'title' => $this->t('Press to set elements like in template. You can manage template settings in the Gridstack Optionset.'),
      ],
    ];

    $form['buttons']['restore'] = [
      '#type' => 'button',
      '#title' => $this->t('Restore settings'),
      '#attributes' => [
        'id' => 'pg-action-restore',
        'class' => ['paragraphs-gridstack-action'],
        'title' => $this->t('Press to restore settings of the latest revision.'),
      ],
    ];

    //grid_columns
    //grid_json_template
    //grid_json_default
    //grid_json

    //grid_columns_mobile
    //grid_json_mobile_template
    //grid_json_mobile_default
    //grid_json_mobile

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

    // @TODO Why attachment to the first field?
    $form['grid_json']['#attached']['library'][] = 'tau_gridstack/paragraphs_gridstack';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(Paragraph $paragraph) {
    // @TODO Looks like fucking bullshit, need to rewrite.
//    $grid_columns = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_columns');
//    $grid_json_template = $paragraph->getBehaviorSetting($this->getPluginId(), 'template_desktop');
//    $grid_json_default = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_default');
//    $grid_json = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json');
//    $grid_columns_mobile = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_columns_mobile');
//    $grid_json_mobile_template = $paragraph->getBehaviorSetting($this->getPluginId(), 'template_mobile');
//    $grid_json_mobile_default = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_mobile_default');
//    $grid_json_mobile = $paragraph->getBehaviorSetting($this->getPluginId(), 'grid_json_mobile');
    return [
//      $grid_columns ? $this->t('Columns: @element', ['@element' => $grid_columns]) : '56',
//      $grid_json_template ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_template]) : '',
//      $grid_json_default ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_default]) : '',
//      $grid_json ? $this->t('GridStack JSON: @element', ['@element' => $grid_json]) : '',
//      $grid_columns_mobile ? $this->t('Columns: @element', ['@element' => $grid_columns_mobile]) : '12',
//      $grid_json_mobile_template ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_mobile_template]) : '',
//      $grid_json_mobile_default ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_mobile_default]) : '',
//      $grid_json_mobile ? $this->t('GridStack JSON: @element', ['@element' => $grid_json_mobile]) : '',
    ];
  }

  /**
   * Return array of gridstack options sets.
   *
   * @return array
   *   Array of options.
   */
  public function buildOptions() {
    $options = [];
    $optionsets = $this->gridstackOptionsetsManager->listAll();

    foreach ($optionsets as $optionset) {
      $config = $this->configFactory->get($optionset);
      $options[$optionset] = $config->get('label');
    }

    return $options;
  }

}
