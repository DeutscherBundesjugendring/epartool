<?php
/**
 * InputTags
 * @desc    Class of relation between inputs and tags
 * @author  Jan Suchandt
 */
class Model_InputsTags extends Model_DbjrBase {
  protected $_name = 'inpt_tgs';
  protected $_primary = array(
    'tid', 'tg_nr'
  );

  protected $_referenceMap = array(
    'Inputs' => array(
      'columns' => 'tid',
      'refTableClass' => 'Model_Inputs',
      'refColumns' => 'tid',
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ),
    'Tags' => array(
      'columns' => 'tg_nr',
      'refTableClass' => 'Model_Tags',
      'refColumns' => 'tg_nr',
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ),
  );
  
  /**
   * Deletes entries by Inputs ID
   *
   * @param integer $tid
   * @return integer Number of rows deleted
   */
  public function deleteByInputsId($tid) {
    $where = $this->getDefaultAdapter()
      ->quoteInto($this->_primary[0] . ' = ?', $tid);
    return $this->delete($where);
  }
  
  /**
   * Inserts entries by Inputs ID
   *
   * @param integer $tid Inputs ID
   * @param array $data Array of Tag IDs
   * @return integer Number of rows inserted
   */
  public function insertByInputsId($tid, $data) {
    $inserted = array();
    foreach ($data as $tag_nr) {
      $inserted[] = $this->insert(array(
        'tid' => $tid,
        'tg_nr' => $tag_nr
      ));
    }
    return count($inserted);
  }
  
  /**
   * Checks whether a tag (tg_nr) is already used with inputs
   *
   * @param integer $tg_nr
   * @return boolean
   */
  public function tagExists($tg_nr) {
    $select = $this->select();
    $select->where('tg_nr = ?', $tg_nr);
    $rowSet = $this->fetchAll($select);
    if ($rowSet->count() > 0) {
      return true;
    } else {
      return false;
    }
  }
}
