<?php
/**
 * InputTags
 * @desc    Class of relation between inputs and tags
 * @author  Jan Suchandt
 */
class Model_InputsTags extends Zend_Db_Table_Abstract {
  protected $_name = 'inpt_tgs';
  protected $_primary = array(
    'tid', 'tg_nr'
  );

  protected $_referenceMap = array(
    'Inputs' => array(
      'columns' => 'tid', 'refTableClass' => 'Model_Inputs', 'refColumns' => 'tid'
    ),
    'Tags' => array(
      'columns' => 'tg_nr', 'refTableClass' => 'Model_Tags', 'refColumns' => 'tg_nr'
    ),
  );
}
