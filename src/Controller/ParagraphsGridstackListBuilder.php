<?php

namespace Drupal\paragraphs_gridstack\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of ParagraphsGridstack entities.
 *
 * List Controllers provide a list of entities in a tabular form. The base
 * class provides most of the rendering logic for us. The key functions
 * we need to override are buildHeader() and buildRow(). These control what
 * columns are displayed in the table, and how each row is displayed
 * respectively.
 *
 * Drupal locates the list controller by looking for the "list" entry under
 * "controllers" in our entity type's annotation. We define the path on which
 * the list may be accessed in our module's *.routing.yml file. The key entry
 * to look for is "_entity_list". In *.routing.yml, "_entity_list" specifies
 * an entity type ID. When a user navigates to the URL for that router item,
 * Drupal loads the annotation for that entity type. It looks for the "list"
 * entry under "controllers" for the class to load.
 */
class ParagraphsGridstackListBuilder extends ConfigEntityListBuilder {
  use DescriptionTemplateTrait;

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'paragraphs_gridstack';
  }

  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['machine_name'] = $this->t('Machine Name');
    $header['float'] = $this->t('Float');
    $header['allowCustomClass'] = $this->t('Allow custom classes for items');
    $header['allowRoundedClass'] = $this->t('Allow making items circle');

    return $header + parent::buildHeader();
  }

  /**
   * Builds a row for an entity in the entity listing.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for which to build the row.
   *
   * @return array
   *   A render array of the table row for displaying the entity.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildRow(EntityInterface $entity) {
    $boolean = [1 => $this->t('True'), 0 => $this->t('False')];
    $row['label'] = $entity->label();
    $row['machine_name'] = $entity->id();
    $row['float'] = $boolean[(int) $entity->float];
    $row['allowCustomClass'] = $boolean[(int) $entity->allowCustomClass];
    $row['allowRoundedClass'] = $boolean[(int) $entity->allowRoundedClass];

    return $row + parent::buildRow($entity);
  }

  /**
   * Adds some descriptive text to our entity list.
   *
   * Typically, there's no need to override render(). You may wish to do so,
   * however, if you want to add markup before or after the table.
   *
   * @return array
   *   Renderable array.
   */
  public function render() {
    $build = $this->description();
    $build[] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if (isset($operations['edit'])) {
      $operations['edit']['title'] = $this->t('Configure');
    }

    if ($entity->id() == 'default') {
      unset($operations['delete'], $operations['edit']);
    }

    return $operations;
  }

}
