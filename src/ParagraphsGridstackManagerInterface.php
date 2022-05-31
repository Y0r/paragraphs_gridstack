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
   * Returns a mutable configuration object for a given name.
   *
   * Should not be used for config that will have runtime effects. Therefore it
   * is always loaded override free.
   *
   * @param string $name
   *   The name of the configuration object to construct.
   *
   * @return \Drupal\Core\Config\Config
   *   A configuration object.
   */
  public function getEditable($name);

  /**
   * Gets configuration object names starting with a given prefix.
   *
   * @see \Drupal\Core\Config\StorageInterface::listAll()
   *
   * @param string $prefix
   *   (optional) The prefix to search for. If omitted, all configuration object
   *   names that exist are returned.
   *
   * @return array
   *   An array containing matching configuration object names.
   */
  public function listAll();

}
