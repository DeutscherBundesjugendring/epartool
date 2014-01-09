<?php
/**
 * Tags
 * @desc    Class of Folders
 * @author  Karsten Tackmann
 */
class Model_Directories  extends NP_Db_Table_NestedSet
{
    protected $_name = 'dirs';
    protected $_primary = 'id';
    protected $_left = 'left';
    protected $_right = 'right';
}
