<?php

namespace Drupal\paragraphs_gridstack;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the ParagraphsGridstack entity.
 *
 * @see \Drupal\paragraphs_gridstack\Entity\ParagraphsGridstack
 */
class ParagraphsGridstackAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation == 'view' || $account->hasPermission('administer paragraphs_gridstack')) {
      return AccessResult::allowed();
    }
    return parent::checkAccess($entity, $operation, $account);
  }

}
