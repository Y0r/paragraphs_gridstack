<?php

/**
 * Declaration the breakpoints manager interface.
 */
interface GridstackBreakpointsManagerInterface {

  /**
   * Returns all breakpoint providers and groups.
   *
   * @return array
   */
  function getBreakpointsProviders(): array;

  /**
   * Return a grouped array of the breakpoints providers.
   *
   * @return array
   *   Grouped array of theme and modules with breakpoints.
   */
  function getBreakpointsProvidersList(): array;

  /**
   * Return breakpoints by theme/module name.
   *
   * @param string $provider
   *   Module or theme machine name.
   *
   * @return array
   *   Breakpoints if exists.
   */
  function getBreakpointsByProvider(string $provider): array;

  /**
   * Returns media queries of the breakpoint.
   *
   * @param \Drupal\breakpoint\BreakpointInterface[] $breakpoints
   *   Breakpoint object.
   *
   * @return array
   *   Array of the media queries.
   */
  function getBreakpointsMediaQuery(array $breakpoints): array;

  /**
   * Returns default breakpoints provider.
   *
   * If current main theme have breakpoints it should be used as default.
   * Otherwise, return a 'paragraph_gridstack' as breakpoints provider.
   *
   * @return string
   *   Machine name of the provider.
   */
  function getDefaultBreakpointsProvider(): string;

}
