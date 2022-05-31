<?php

namespace Drupal\paragraphs_gridstack;

/**
 * Defines re-usable services and functions for Paragraph's Gridstack.
 */
interface ParagraphsGridstackManagerInterface {

  /**
   * Returns an immutable configuration object for a given name.
   *
   * @param string $name
   *   The name of the configuration object to construct.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   A configuration object.
   */
  public function get($name);

  /**
   * Gets configuration object names starting with a given prefix.
   *
   * @see \Drupal\Core\Config\StorageInterface::listAll()
   *
   * @return array
   *   An array containing matching configuration object names.
   */
  public function listAll();

}
