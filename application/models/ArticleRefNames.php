<?php
/**
 * Model ArticleRefNames
 *
 */
class Model_ArticleRefNames extends Model_DbjrBase {
  protected $_name = 'articles_refnm';
  protected $_primary = array('ref_nm', 'lng');

  protected $_referenceMap = array(
    'Articles' => array(
      'columns' => 'ref_nm',
      'refTableClass' => 'Model_Articles',
      'refColumns' => 'ref_nm'
    )
  );
  
  /**
   * Returns all rows ordered by field 'ref_nm'
   * @return Zend_Db_Table_Rowset
   */
  public function getAll() {
    $select = $this->select();
    $select->order('ref_nm');
    return $this->fetchAll($select);
  }
}
?>