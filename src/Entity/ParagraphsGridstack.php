<?php

namespace Drupal\paragraphs_gridstack\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the config entity.
 *
 * The lines below, starting with '@ConfigEntityType,' are a plugin annotation.
 * These define the entity type to the entity type manager.
 *
 * The properties in the annotation are as follows:
 *  - id: The machine name of the entity type.
 *  - label: The human-readable label of the entity type. We pass this through
 *    the "@Translation" wrapper so that the multilingual system may
 *    translate it in the user interface.
 *  - handlers: An array of entity handler classes, keyed by handler type.
 *    - access: The class that is used for access checks.
 *    - list_builder: The class that provides listings of the entity.
 *    - form: An array of entity form classes keyed by their operation.
 *  - entity_keys: Specifies the class properties in which unique keys are
 *    stored for this entity type. Unique keys are properties which you know
 *    will be unique, and which the entity manager can use as unique in database
 *    queries.
 *  - links: entity URL definitions. These are mostly used for Field UI.
 *    Arbitrary keys can set here. For example, User sets cancel-form, while
 *    Node uses delete-form.
 *
 * @see http://previousnext.com.au/blog/understanding-drupal-8s-config-entities
 * @see annotation
 * @see Drupal\Core\Annotation\Translation
 *
 * @ConfigEntityType(
 *   id = "paragraphs_gridstack",
 *   label = @Translation("Paragraphs Gridstack optionset"),
 *   handlers = {
 *     "access" = "Drupal\paragraphs_gridstack\ParagraphsGridstackAccessControlHandler",
 *     "list_builder" = "Drupal\paragraphs_gridstack\Controller\ParagraphsGridstackListBuilder",
 *     "form" = {
 *       "add" = "Drupal\paragraphs_gridstack\Form\ParagraphsGridstackAddForm",
 *       "edit" = "Drupal\paragraphs_gridstack\Form\ParagraphsGridstackEditForm",
 *       "delete" = "Drupal\paragraphs_gridstack\Form\ParagraphsGridstackDeleteForm"
 *     }
 *   },
 *   config_prefix = "optionset",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/media/paragraphs_gridstack/manage/{paragraphs_gridstack}",
 *     "delete-form" = "/admin/config/media/paragraphs_gridstack/manage/{paragraphs_gridstack}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "label",
 *     "langcode",
 *     "float",
 *     "allow_custom_class",
 *     "allow_rounded_class"
 *   }
 * )
 */
class ParagraphsGridstack extends ConfigEntityBase {

  /**
   * The ID.
   *
   * @var string
   */
  public $id;

  /**
   * The config UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The config label.
   *
   * @var string
   */
  public $label;

  /**
   * Widgets will go upward direction to fill container's empty place.
   *
   * @var bool
   */
  public $float;

  /**
   * Allow custom classes for elements.
   *
   * @var bool
   */
  public $allow_custom_class;

  /**
   * Allow round class for elements.
   *
   * @var bool
   */
  public $allow_rounded_class;

}
