<?php

namespace Drupal\paragraphs_gridstack;

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
  public function getBreakpointsProviders(): array {
    $groups = $this->breakpointManager->getGroups();

    $groups_providers = [];
    foreach ($groups as $key => $label) {
      $providers = $this->breakpointManager->getGroupProviders($key);
      foreach ($providers as $provider_name => $provider_type) {
        // Concat provider and group identifiers if group specified.
        $machine_name = $provider_name;
        if ($provider_name !== $key) {
          $machine_name = strtolower("$provider_name.$key");
        }

        $groups_providers[$machine_name] = [
          'type' => $provider_type,
          'provider' => $provider_name,
          'group' => $key,
          'label' => $label,
        ];
      }
    }

    // Sort by keys.
    ksort($groups_providers);
    return $groups_providers;
  }

  /**
   * {@inheritdoc}
   */
  public function getBreakpointsProvidersList(): array {
    $options = [];
    $breakpoints_providers = $this->getBreakpointsProviders();

    foreach ($breakpoints_providers as $machine_name => $breakpoints_provider) {
      $provider_label = ucwords(str_replace(['_', '.'], ' ', $machine_name));
      $options[t(ucfirst($breakpoints_provider['type']))->render()][t($machine_name)->render()] = $provider_label;
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getBreakpointsByCondition(string $provider): array {
    $breakpoints = [];
    $breakpoints_providers = $this->getBreakpointsProviders();
    $breakpoints_provider = $breakpoints_providers[$provider] ?? NULL;

    if (empty($breakpoints_provider)) {
      foreach ($breakpoints_providers as $breakpoints_data) {
        if ($breakpoints_data['provider'] == $provider) {
          $breakpoints_provider = $breakpoints_data;
          break;
        }
      }
    }

    if (!empty($breakpoints_provider['group'])) {
      $breakpoints = $this->breakpointManager
        ->getBreakpointsByGroup($breakpoints_provider['group']);
    }

    return $breakpoints;
  }

  /**
   * {@inheritdoc}
   */
  public function getBreakpointsMediaQuery(array $breakpoints): array {
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
  public function getDefaultBreakpointsProvider(): string {
    return 'paragraphs_gridstack';
  }

}
