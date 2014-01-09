<?php

class Model_Inputs_DashBoard  extends Model_DbjrBase
{
    protected $_name = 'inpt';
    protected $_primary = 'tid';

   protected $_dependentTables = array('Model_InputsTags');

}
