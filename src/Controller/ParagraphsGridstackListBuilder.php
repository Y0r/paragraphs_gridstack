<?php

namespace Drupal\paragraphs_gridstack\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\examples\Utility\DescriptionTemplateTrait;

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
 *
 * @ingroup config_entity_example
 */
class ParagraphsGridstackListBuilder extends ConfigEntityListBuilder {
  use DescriptionTemplateTrait;

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'config_entity_example';
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
    $header['perBundle'] = $this->t('Per Bundle');
    $header['perEntity'] = $this->t('Per Entity');
    $header['freeSpace'] = $this->t('Sticky/Float');
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
    $row['label'] = $entity->label();
    $row['machine_name'] = $entity->id();
    $row['perBundle'] = $entity->perBundle;
    $row['perEntity'] = $entity->perEntity;
    $row['freeSpace'] = $entity->freeSpace;
    $row['allowCustomClass'] = $entity->allowCustomClass;
    $row['allowRoundedClass'] = $entity->allowRoundedClass;

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

}
