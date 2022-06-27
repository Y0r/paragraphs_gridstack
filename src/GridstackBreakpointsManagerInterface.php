<?php

namespace Drupal\paragraphs_gridstack;

/**
 * Declaration the breakpoints manager interface.
 */
interface GridstackBreakpointsManagerInterface {

  /**
   * Returns all breakpoint providers and groups.
   *
   * @return array
   *   Array of the breakpoints groups providers.
   */
  public function getBreakpointsProviders(): array;

  /**
   * Return a grouped array of the breakpoints providers.
   *
   * @return array
   *   Grouped array of theme and modules with breakpoints.
   */
  public function getBreakpointsProvidersList(): array;

  /**
   * Return breakpoints by group and theme/module name.
   *
   * @param string $provider
   *   Module or theme machine name.
   *
   * @return array
   *   Breakpoints if exists.
   */
  public function getBreakpointsByCondition(string $provider): array;

  /**
   * Returns media queries of the breakpoint.
   *
   * @param \Drupal\breakpoint\BreakpointInterface[] $breakpoints
   *   Breakpoint object.
   *
   * @return array
   *   Array of the media queries.
   */
  public function getBreakpointsMediaQuery(array $breakpoints): array;

  /**
   * Returns default breakpoints provider.
   *
   * @return string
   *   By default, returns 'paragraphs_gridstack' as provider.
   */
  public function getDefaultBreakpointsProvider(): string;

}
