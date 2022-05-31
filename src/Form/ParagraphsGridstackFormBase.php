<?php

namespace Drupal\paragraphs_gridstack\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ParagraphsGridstackFormBase.
 *
 * Typically, we need to build the same form for both adding a new entity,
 * and editing an existing entity.
 */
class ParagraphsGridstackFormBase extends EntityForm {

  /**
   * An entity query factory for the ParagraphsGridstack entity type.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Construct the ParagraphsGridstackFormBase.
   *
   * For simple entity forms, there's no need for a constructor. Our form
   * base, however, requires an entity query factory to be injected into it
   * from the container. We later use this query factory to build an entity
   * query for the exists() method.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   An entity query factory for the ParagraphsGridstack entity type.
   */
  public function __construct(EntityStorageInterface $entity_storage) {
    $this->entityStorage = $entity_storage;
  }

  /**
   * Factory method for ParagraphsGridstackFormBase.
   *
   * When Drupal builds this class it does not call the constructor directly.
   * Instead, it relies on this method to build the new object. Why? The class
   * constructor may take multiple arguments that are unknown to Drupal. The
   * create() method always takes one parameter -- the container. The purpose
   * of the create() method is twofold: It provides a standard way for Drupal
   * to construct the object, meanwhile it provides you a place to get needed
   * constructor parameters from the container.
   *
   * In this case, we ask the container for an entity query factory. We then
   * pass the factory to our class as a constructor parameter.
   */
  public static function create(ContainerInterface $container) {
    $form = new static($container->get('entity_type.manager')->getStorage('paragraphs_gridstack'));
    $form->setMessenger($container->get('messenger'));
    return $form;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::form().
   *
   * Builds the entity add/edit form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   An associative array containing the ParagraphsGridstack add/edit form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get anything we need from the base class.
    $form = parent::buildForm($form, $form_state);

    // Drupal provides the entity to us as a class variable. If this is an
    // existing entity, it will be populated with existing values as class
    // variables. If this is a new entity, it will be a new object with the
    // class of our entity. Drupal knows which class to call from the
    // annotation on our ParagraphsGridstack class.
    $paragraphsGridstack = $this->entity;

    // Build the form.
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $paragraphsGridstack->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('Machine name'),
      '#default_value' => $paragraphsGridstack->id(),
      '#machine_name' => [
        'exists' => [$this, 'exists'],
        'replace_pattern' => '([^a-z0-9_]+)|(^custom$)',
        'error' => 'The machine-readable name must be unique, and can only contain lowercase letters, numbers, and underscores. Additionally, it can not be the reserved word "custom".',
      ],
      '#disabled' => !$paragraphsGridstack->isNew(),
    ];
    $form['float'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Float setting: widgets will go upward direction to fill container's empty place"),
      '#default_value' => $paragraphsGridstack->float,
    ];
    $form['allowCustomClass'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow custom classes for items'),
      '#default_value' => $paragraphsGridstack->allowCustomClass,
    ];
    $form['allowRoundedClass'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow making items circle'),
      '#default_value' => $paragraphsGridstack->allowRoundedClass,
    ];

    // Return the form.
    return $form;
  }

  /**
   * Checks for an existing ParagraphsGridstack.
   *
   * @return bool
   *   TRUE if this format already exists, FALSE otherwise.
   */
  public function exists($entity_id, array $element, FormStateInterface $form_state) {
    $query = $this->entityStorage->getQuery();
    $result = $query->condition('id', $element['#field_prefix'] . $entity_id)
      ->execute();
    return (bool) $result;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   *
   * Saves the entity. This is called after submit() has built the entity from
   * the form values. Do not override submit() as save() is the preferred
   * method for entity form controllers.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function save(array $form, FormStateInterface $form_state) {
    // EntityForm provides us with the entity we're working on.
    $paragraphsGridstack = $this->getEntity();

    // Drupal already populated the form values in the entity object. Each
    // form field was saved as a public variable in the entity class. PHP
    // allows Drupal to do this even if the method is not defined ahead of
    // time.
    $status = $paragraphsGridstack->save();

    // Grab the URL of the new entity. We'll use it in the message.
    $url = $paragraphsGridstack->toUrl();

    // Create an edit link.
    $edit_link = Link::fromTextAndUrl($this->t('Edit'), $url)->toString();

    if ($status == SAVED_UPDATED) {
      // If we edited an existing entity...
      $this->messenger()->addMessage($this->t('ParagraphsGridstack %label has been updated.', ['%label' => $paragraphsGridstack->label()]));
      $this->logger('contact')->notice(
        'ParagraphsGridstack %label has been updated.',
        ['%label' => $paragraphsGridstack->label(), 'link' => $edit_link]
      );
    }
    else {
      // If we created a new entity...
      $this->messenger()->addMessage($this->t('ParagraphsGridstack %label has been added.', ['%label' => $paragraphsGridstack->label()]));
      $this->logger('contact')->notice(
        'Robot %label has been added.',
        ['%label' => $paragraphsGridstack->label(), 'link' => $edit_link]
      );
    }

    // Redirect the user back to the listing route after the save operation.
    $form_state->setRedirect('entity.paragraphs_gridstack.list');
  }

}
