<?php
/**
 * Configs
 *
 * @description   Model für Konfigurationen
 * @author        Jan Suchandt
 */
class Configs extends Zend_Db_Table_Abstract {
  protected $_name = 'configs';
  protected $_primary = 'configs_id';

  /**
   * Finde Pizza und lade Zutaten
   *
   * @param $id integer Primärschlüssel der zu ladenden Pizza
   */
  public function get($id) {
    /*
    // Filtere Parameter, so dass nur Ziffern übrig bleiben
    $id = Zend_Filter::get($id, 'Int');
    
    // Lade Daten für Pizza
    $data = $this->find($id)->current();
    
    // Prüfe auf fehlenden Datensatz
    if (empty($data)) {
        throw new Zend_Db_Exception('Pizza ID "' . $id . '" ist ungültig');
    }
    
    // Konvertiere Daten ins Array-Format
    $data = $data->toArray();
    
    // Erstelle neues Selectobjekt
    $select = $this->getAdapter()->select();
    $select->from('ingredients');
    $select->join(
        'pizza_ingredients', 'pi2in_ingredient_id = ingredient_id', array()
    );
    $select->where('pi2in_pizza_id = ?', $id);
    
    // Hole Daten
    $data['ingredients'] = $this->getAdapter()->fetchAll($select);
    
    // Gebe alle Daten zurück
    return $data;
     */

    return 'hi';
  }

  /**
   * Liefert alle Konfigurationen mit Kategorie
   *
   */
  public function getAll() {
    return $this->getByGroup(0);
  }

  /**
   * Liefert alle Konfigurationen mit Kategorie
   *
   */
  public function getByGroup($groupId) {
    $select = $this->getAdapter()->select();
    $select
        ->from(array(
              'c' => 'configs'
            ),
            array(
              'configs_id',
              'configs_name',
              'configs_value',
              'configs_comments',
              'configs_modified'
            ));
    $select
        ->joinLeft(
            array(
              'cg' => 'configs_groups'
            ), 'c.configs_groups_id = cg.configs_groups_id',
            array(
              'configs_groups_name'
            ));
    if (!empty($groupId)) {
      $select->where('cg.configs_groups_id  = ?', $groupId);
    }
    $select
        ->order(
            array(
              'cg.configs_groups_name', 'c.configs_name'
            ));

    $configs = $this->getAdapter()->fetchAll($select);
    return $configs;
  }

  /**
   * Liefert alle Konfiguration mit Kategorie anhand der ID
   *
   */
  public function getById($id) {
    $select = $this->getAdapter()->select();
    $select
        ->from(array(
              'c' => 'configs'
            ),
            array(
              'configs_id',
              'configs_name',
              'configs_value',
              'configs_comments',
              'configs_modified'
            ));
    $select
        ->joinLeft(
            array(
              'cg' => 'configs_groups'
            ), 'c.configs_groups_id = cg.configs_groups_id',
            array(
              'configs_groups_name'
            ));
    $select->where('c.configs_id  = ?', $id);

    $configs = $this->getAdapter()->fetchRow($select);
    return $configs;
  }
}

