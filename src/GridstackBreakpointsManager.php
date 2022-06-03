<?php

use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides methods for breakpoints management.
 */
class GridstackBreakpointsManager implements GridstackBreakpointsManagerInterface {

  /**
   * Definition of the BreakpointManagerInterface.
   *
   * @var \Drupal\breakpoint\BreakpointManagerInterface
   */
  protected BreakpointManagerInterface $breakpointManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Constructs a BreakpointManagerInterface.
   */
  public function __construct(BreakpointManagerInterface $breakpoint_manager, ConfigFactoryInterface $config_factory) {
    $this->breakpointManager = $breakpoint_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  function getBreakpointsProviders(): array {
    $groups = $this->breakpointManager->getGroups();

    $groups_providers = [];
    foreach ($groups as $key => $label) {
      $provider = $this->breakpointManager->getGroupProviders($key);
      $groups_providers[$key] = array_flip($provider);
    }

    return $groups_providers;
  }

  /**
   * {@inheritdoc}
   */
  function getBreakpointsProvidersList(): array {
    $options = [];
    $groups_providers = $this->getBreakpointsProviders();

    foreach ($groups_providers as $group => $provider) {
      $provider_type = key($provider);
      $provider_name = $provider[$provider_type];
      $options[t($provider_type)->render()][t($group)->render()] = $provider_name;
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  function getBreakpointsByProvider(string $provider): array {
    $options = [];
    $groups_providers = $this->getBreakpointsProviders();

    foreach ($groups_providers as $group => $group_provider) {
      $provider_type = key($group_provider);
      $provider_name = $group_provider[$provider_type];
      $options[$provider_name] = $group;
    }

    return $this->breakpointManager->getBreakpointsByGroup($options[$provider]);
  }

  /**
   * {@inheritdoc}
   */
  function getBreakpointsMediaQuery(array $breakpoints): array {
    if (empty($breakpoints)) {
      return [];
    }

    $media_queries = [];
    foreach ($breakpoints as $breakpoint_identifier => $breakpoint) {
      $media_queries[$breakpoint_identifier] = [
        'label' => $breakpoint->getLabel(),
        'group' => $breakpoint->getGroup(),
        'weight' => $breakpoint->getWeight(),
        'provider' => $breakpoint->getProvider(),
        'media_query' => $breakpoint->getMediaQuery(),
        'multipliers' => $breakpoint->getMultipliers(),
      ];
    }

    return $media_queries;
  }

  /**
   * {@inheritdoc}
   */
  function getDefaultBreakpointsProvider(): string {
    $current_theme = $this->configFactory
      ->get('system.theme')
      ->get('default');

    $breakpoints = $this->getBreakpointsByProvider($current_theme);
    if (!empty($this->getBreakpointsMediaQuery($breakpoints))) {
      return $current_theme;
    }

    return 'paragraphs_gridstack';
  }
}
