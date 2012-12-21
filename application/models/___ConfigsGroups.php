<?php
/**
 * Configs
 *
 * @description   Model für Konfigurationsgruppen
 * @author        Jan Suchandt
 */
class ConfigsGroups extends Zend_Db_Table_Abstract {
  protected $_name = 'configs_groups';
  protected $_primary = 'configs_groups_id';

  /**
   * Liefert alle Konfigurationsgruppen
   *
   */
  public function getAll() {
    $select = $this->getAdapter()->select();
    $select
        ->from(array(
              'cg' => 'configs_groups'
            ),
            array(
              'configs_groups_id', 'configs_groups_name'
            ));
    $select->order(array(
          'cg.configs_groups_name'
        ));

    $groups = $this->getAdapter()->fetchAll($select);
    return $groups;
  }
}

