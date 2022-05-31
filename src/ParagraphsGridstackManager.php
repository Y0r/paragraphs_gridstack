<?php

namespace Drupal\paragraphs_gridstack;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Defines re-usable services and functions for Paragraph's Gridstack.
 */
class ParagraphsGridstackManager implements ParagraphsGridstackManagerInterface {

  const PREFIX = 'paragraphs_gridstack.optionset.';

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a new Service.
   */
  public function __construct(ConfigFactoryInterface $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public function get($name = 'default') {
    return $this->config->get($this::PREFIX . $name);
  }

  /**
   * {@inheritdoc}
   */
  public function getEditable($name = 'default') {
    return $this->config->getEditable($this::PREFIX . $name);
  }

  /**
   * {@inheritdoc}
   */
  public function listAll() {
    return $this->config->listAll($this::PREFIX);
  }

}
