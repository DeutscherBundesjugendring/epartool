<?php

class Model_Inputs_VotingPrepare  extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'inpt';
    protected $_primary = 'tid';

   protected $_dependentTables = array('Model_InputsTags');

}
