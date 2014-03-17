<?php
/**
 * NP-DbTableNestedSet
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

require_once 'Zend/Db/Table.php';

/**
 * Class that extends capabilities of Zend_Db_Table class,
 * providing API for managing some Nested set table in
 * database.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License
 */
class NP_Db_Table_NestedSet extends Zend_Db_Table
{
    const LEFT_COL  = 'left';
    const RIGHT_COL = 'right';

    const FIRST_CHILD  = 'firstChild';
    const LAST_CHILD   = 'lastChild';
    const NEXT_SIBLING = 'nextSibling';
    const PREV_SIBLING = 'prevSibling';

    const LEFT_TBL_ALIAS  = 'node';
    const RIGHT_TBL_ALIAS = 'parent';

    /**
     * Valid objective node positions.
     *
     * @var array
     */
    protected static $_validPositions = array(
        self::FIRST_CHILD,
        self::LAST_CHILD,
        self::NEXT_SIBLING,
        self::PREV_SIBLING
    );

    /**
     * Left column name in nested table.
     *
     * @var string
     */
    protected $_left;

    /**
     * Right column name in nested table.
     *
     * @var string
     */
    protected $_right;

    /**
     * Internal cache of nested data (left, right, width)
     * retrieved from some nodes.
     *
     * @var array
     */
    protected $_nestedDataCache = array();

    /**
     * __construct() - For concrete implementation of NP_Db_Table_NestedSet
     *
     * @param string|array $config string can reference a Zend_Registry key for a db adapter
     *                             OR it can reference the name of a table
     * @param  array|Zend_Db_Table_Definition $definition
     * @return void
     */
    public function __construct($config = array(), $definition = null)
    {
        parent::__construct($config, $definition);

        $this->_setupPrimaryKey();
        $this->_setupLftRgt();
    }

    /**
     * Defined by Zend_Db_Table_Abstract.
     *
     * @param  array                  $options
     * @return Zend_Db_Table_Abstract
     */
    public function setOptions(Array $options)
    {
        if (isset($options[self::LEFT_COL])) {
            $this->_left = (string) $options[self::LEFT_COL];
        }
        if (isset($options[self::RIGHT_COL])) {
            $this->_right = (string) $options[self::RIGHT_COL];
        }

        return parent::setOptions($options);
    }

    /**
     * Defined by Zend_Db_Table_Abstract.
     *
     * @return void
     */
    protected function _setupPrimaryKey()
    {
        parent::_setupPrimaryKey();

        if (count($this->_primary) > 1) { //Compound key?
            include_once 'NP/Db/Table/NestedSet/Exception.php';
            throw new NP_Db_Table_NestedSet_Exception('Tables with compound primary key are not currently supported.');
        }
    }

    /**
     * Validating supplied "left" and "right" columns.
     *
     * @return void
     */
    protected function _setupLftRgt()
    {
        if (!$this->_left || !$this->_right) {
            include_once 'NP/Db/Table/NestedSet/Exception.php';
            throw new NP_Db_Table_NestedSet_Exception('Both "left" and "right" column names must be supplied.');
        }

        $this->_setupMetadata();

        if (count(array_intersect(array($this->_left, $this->_right), array_keys($this->_metadata))) < 2) {
            include_once 'NP/Db/Table/NestedSet/Exception.php';
            throw new NP_Db_Table_NestedSet_Exception('Supplied "left" and "right" were not found.');
        }
    }

    /**
     * Defined by Zend_Db_Table_Abstract.
     *
     * @param  string $key The specific info part to return OPTIONAL
     * @return mixed
     */
    public function info($key = null)
    {
        $nestedSetInfo = array(
            self::LEFT_COL=>$this->_left,
            self::RIGHT_COL=>$this->_right
        );

        if ($key === null) {
            return array_merge(parent::info(), $nestedSetInfo);
        } else {
            if (array_key_exists($key, $nestedSetInfo)) {
                return $nestedSetInfo[$key];
            } else {
                return parent::info($key);
            }
        }
    }

    /**
     * Overriding fetchAll() method defined by Zend_Db_Table_Abstract.
     *
     * @param string|array|Zend_Db_Table_Select $where       OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param bool                              $getAsTree   OPTIONAL Whether to retrieve nodes as tree.
     * @param string                            $parentAlias OPTIONAL If this argument is supplied, additional column,
     *                                                      named after value of this argument, will be returned,
     *                                                      containing id of a parent node will be included in result set.
     * @param  string|array                  $order  OPTIONAL An SQL ORDER clause.
     * @param  int                           $count  OPTIONAL An SQL LIMIT count.
     * @param  int                           $offset OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAll($where = null, $getAsTree = false, $parentAlias = null, $order = null, $count = null, $offset = null)
    {
        if ($getAsTree == true) { //If geeting nodes as tree, other arguments are omitted.

            return $this->getTree($where);
        } elseif ($parentAlias != null) {
            return parent::fetchAll($this->_getSelectWithParent($where, $parentAlias, $order, $count, $offset));
        } else {
            return parent::fetchAll($where, $order, $count, $offset);
        }
    }

    /**
     * Overriding fetchRow() method defined by Zend_Db_Table_Abstract.
     *
     * @param string|array|Zend_Db_Table_Select $where       OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string                            $parentAlias OPTIONAL If this argument is supplied, additional column,
     *                                                      named after value of this argument, will be returned,
     *                                                      containing id of a parent node will be included in result set.
     * @param  string|array                    $order OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function fetchRow($where = null, $parentAlias = null, $order = null)
    {
        if ($parentAlias != null) {
            return parent::fetchRow($this->_getSelectWithParent($where, $parentAlias, $order));
        } else {
            return parent::fetchRow($where, $order);
        }
    }

    /**
     * Generates and returns SQL query that is used for fetchAll() and
     * fetchRow() methods, in case $parentAlias param is supplied.
     *
     * @param  string|array|Zend_Db_Table_Select|null $where       An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param  string|null                            $parentAlias Additional column, named after value of this argument, will be returned, containing id of a parent node will be included in result set.
     * @param  string|array|null                      $order       An SQL ORDER clause.
     * @param  int|null                               $count       OPTIONAL An SQL LIMIT count.
     * @param  int|null                               $offset      OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Select
     */
    protected function _getSelectWithParent($where, $parentAlias, $order, $count = null, $offset = null)
    {
        $parentAlias = (string) $parentAlias;

        $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
        $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

        $parentSelect = $this->select()
            ->from($this->_name, array($this->_primary[1]))
            ->where(self::LEFT_TBL_ALIAS . '.' . $leftCol . ' BETWEEN ' . $leftCol . '+1 AND ' . $rightCol)
            ->order("$this->_left DESC")
            ->limit(1);

        $select = $this->select()->from(array(self::LEFT_TBL_ALIAS => $this->_name), array('*', $parentAlias => "($parentSelect)"));

        if ($where !== null) {
            $this->_where($select, $where);
        }

        if ($order !== null) {
            $this->_order($select, $order);
        }

        if ($count !== null || $offset !== null) {
            $select->limit($count, $offset);
        }

        return $select;
    }

    /**
     * Gets whole tree, including depth information.
     *
     * @param mixed An SQL WHERE clause or Zend_Db_Table_Select object.
     * @return array
     */
    public function getTree($where = null)
    {
        $primary = $this->getAdapter()->quoteIdentifier($this->_primary[1]);
        $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
        $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

        $select = $this->select()->setIntegrityCheck(false)
            ->from(
                array(self::LEFT_TBL_ALIAS => $this->_name),
                array(self::LEFT_TBL_ALIAS . '.*', 'depth' => new Zend_Db_Expr('COUNT(' . self::RIGHT_TBL_ALIAS . '.' . $primary . ') - 1'))
            )
            ->join(
                array(self::RIGHT_TBL_ALIAS => $this->_name),
                '(' . self::LEFT_TBL_ALIAS . '.' . $leftCol . ' BETWEEN ' . self::RIGHT_TBL_ALIAS . '.' . $leftCol . ' AND ' . self::RIGHT_TBL_ALIAS . '.' . $rightCol . ')',
                array()
            )
            ->group(self::LEFT_TBL_ALIAS . '.' . $this->_primary[1])
            ->order(self::LEFT_TBL_ALIAS . '.' . $this->_left);

        if ($where !== null) {
            $this->_where($select, $where);
        }

        return parent::fetchAll($select);
    }

    /**
     * Overriding insert() method defined by Zend_Db_Table_Abstract.
     *
     * @param array Submitted data.
     * @param int|null Objective node id (optional).
     * @param string Position regarding on objective node (optional).
     * @return mixed
     */
    public function insert(array $data, $objectiveNodeId = null, $position = self::LAST_CHILD)
    {
        if (!$this->_checkNodePosition($position)) {
            include_once 'NP/Db/Table/NestedSet/Exception.php';
            throw new NP_Db_Table_NestedSet_Exception('Invalid node position is supplied.');
        }

        $data = array_merge($data, $this->_getLftRgt($objectiveNodeId, $position));

        return parent::insert($data);
    }

    /**
     * Updates info of some node.
     *
     * @param array Submitted data.
     * @param int Id of a node that is being updated.
     * @param int Objective node id.
     * @param string Position regarding on objective node.
     * @return mixed
     */
    public function updateNode(array $data, $id, $objectiveNodeId, $position = self::LAST_CHILD)
    {
        $id = (int) $id;
        $objectiveNodeId = (int) $objectiveNodeId;

        if (!$this->_checkNodePosition($position)) {
            include_once 'NP/Db/Table/NestedSet/Exception.php';
            throw new NP_Db_Table_NestedSet_Exception('Invalid node position is supplied.');
        }

        if ($objectiveNodeId != $this->_getCurrentObjectiveId($id, $position)) { //Objective node differs?
            $data = array_merge($data, $this->_getLftRgt($objectiveNodeId, $position, $id));
        }

        $primary = $this->getAdapter()->quoteIdentifier($this->_primary[1]);
        $where = $this->getAdapter()->quoteInto($primary . ' = ?', $id, Zend_Db::INT_TYPE);

        return $this->update($data, $where);
    }

    /**
     * Checks whether valid node position is supplied.
     *
     * @param string Position regarding on objective node.
     * @return bool
     */
    private function _checkNodePosition($position)
    {
        if (!in_array($position, self::$_validPositions)) {
            return false;
        }

        return true;
    }

    /**
     * Deletes some node(s) and returns ids of deleted nodes.
     *
     * @param mixed Id of a node.
     * @param bool Whether to delete child nodes, too.
     * @return int The number of affected rows.
     */
    public function deleteNode($id, $cascade = false)
    {
        $retval = 0;

        $id = (int) $id;

        $primary = $this->getAdapter()->quoteIdentifier($this->_primary[1]);

        if (!$cascade) {
            //Deleting node.
            $retval = $this->delete(array($primary . ' = ?'=>$id));
        } else {
            $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
            $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

            $result = $this->getNestedSetData($id);

            $lft = (int) $result['left'];
            $rgt = (int) $result['right'];
            $width = (int) $result['width'];

            //Deleting items.
            $retval = $this->delete("$leftCol BETWEEN $lft AND $rgt");

            $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol - $width")), "$leftCol > $lft");

            $this->update(array($this->_right=>new Zend_Db_Expr("$rightCol - $width")), "$rightCol > $rgt");
        }

        return $retval;
    }

    /**
     * Generates left and right column value, based on id of a
     * objective node.
     *
     * @param int|null Id of a objective node.
     * @param string Position in tree.
     * @param int|null Id of a node for which left and right column values are being generated (optional).
     * @return array
     */
    protected function _getLftRgt($objectiveNodeId, $position, $id = null)
    {
        $lftRgt = array();

        $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
        $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

        $left = null;
        $right = null;

        if ($objectiveNodeId) { //User selected some objective node?
            $objectiveNodeId = (int) $objectiveNodeId;
            $result = $this->getNestedSetData($objectiveNodeId);
            if ($result) {
                $left = (int) $result['left'];
                $right = (int) $result['right'];
            }
        }

        if ($left !== null && $right !== null) { //Existing objective id?
            switch ($position) {
                case self::FIRST_CHILD :
                    $lftRgt[$this->_left] = $left + 1;
                    $lftRgt[$this->_right] = $left + 2;

                    $this->update(array($this->_right=>new Zend_Db_Expr("$rightCol + 2")), "$rightCol > $left");
                    $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol + 2")), "$leftCol > $left");

                    break;
                case self::LAST_CHILD :
                    $lftRgt[$this->_left] = $right;
                    $lftRgt[$this->_right] = $right + 1;

                    $this->update(array($this->_right=>new Zend_Db_Expr("$rightCol + 2")), "$rightCol >= $right");
                    $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol + 2")), "$leftCol > $right");

                    break;
                case self::NEXT_SIBLING :
                    $lftRgt[$this->_left] = $right + 1;
                    $lftRgt[$this->_right] = $right + 2;

                    $this->update(array($this->_right=>new Zend_Db_Expr("$rightCol + 2")), "$rightCol > $right");
                    $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol + 2")), "$leftCol > $right");

                    break;
                case self::PREV_SIBLING :
                    $lftRgt[$this->_left] = $left;
                    $lftRgt[$this->_right] = $left + 1;

                    $this->update(array($this->_right=>new Zend_Db_Expr("$rightCol + 2")), "$rightCol > $left");
                    $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol + 2")), "$leftCol >= $left");

                    break;
            }
        } else {
            $sql = $this->select()->from($this->_name,array('max_rgt'=>new Zend_Db_Expr("MAX($rightCol)")));
            if ($id !== null) {
               $id = (int) $id;
               $primary = $this->getAdapter()->quoteIdentifier($this->_primary[1]);
               $sql->where("$primary != ?", $id, Zend_Db::INT_TYPE);
            }
            $result = $this->_db->fetchRow($sql);

            if (!$result) { //No data? First node...
                $lftRgt[$this->_left] = 1;
            } else {
                $lftRgt[$this->_left] = (int) $result['max_rgt'] + 1;
            }

            $lftRgt[$this->_right] = $lftRgt[$this->_left] + 1;
        }

        return $lftRgt;
    }

    /**
     * Reduces lft and rgt values of some nodes, on which some
     * node that is changing position in tree, or being deleted,
     * has effect.
     *
     * @param mixed Id of a node.
     * @return void
     */
    /*protected function _reduceWidth($id)
    {
        $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
        $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

        $result = $this->getNestedSetData($id);

        if ($result) {
            $left = (int) $result['left'];
            $right = (int) $result['right'];
            $width = (int) $result['width'];

            if ($width > 2) { //Some node that has childs.
                //Updating child nodes.
                $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol - 1"), $this->_right=>new Zend_Db_Expr("$rightCol - 1")), "$leftCol BETWEEN $left AND $right");
            }

            //Updating parent nodes and nodes on higher levels.
            $this->update(array($this->_left=>new Zend_Db_Expr("$leftCol - 2")), "$leftCol > $left AND $rightCol > $right");
            $this->update(array($this->_right=>new Zend_Db_Expr("$rightCol - 2")), "$rightCol > $right");
        }
    }*/

    /**
     * Gets nested set data (left, right, width) for some node.
     *
     * @param  int        $id Id of a node.
     * @return array|null
     */
    public function getNestedSetData($id)
    {
        if (array_key_exists($id, $this->_nestedDataCache)) {
            return $this->_nestedDataCache[$id];
        }

        $primary = $this->getAdapter()->quoteIdentifier($this->_primary[1]);
        $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
        $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

        $sql = $this->select()
            ->from(
                $this->_name,
                array(
                    'left'=>$this->_left,
                    'right'=>$this->_right,
                    'width' => new Zend_Db_Expr("$rightCol - $leftCol + 1")
                )
            )
            ->where($primary . ' = ?', (int) $id, Zend_Db::INT_TYPE);

        $result = $this->_db->fetchRow($sql);
        if ($result) {
            $this->_nestedDataCache[$id] = $result; //Storing result in cache.

            return $result;
        } else {
            return null;
        }
    }

    /**
     * Gets id of some node's current objective node.
     *
     * @param mixed Node id.
     * @param string Position in tree.
     * @return int|null
     */
    protected function _getCurrentObjectiveId($nodeId, $position)
    {
        $primary = $this->getAdapter()->quoteIdentifier($this->_primary[1]);
        $leftCol = $this->getAdapter()->quoteIdentifier($this->_left);
        $rightCol = $this->getAdapter()->quoteIdentifier($this->_right);

        $sql = $this->select()
            ->from(
                array('node' => $this->_name),
                array($this->_primary[1])
            )
            ->join(array('current'=>$this->_name), '', array());

        switch ($position) {
            case self::FIRST_CHILD :
                $sql->where("current.$leftCol BETWEEN node.$leftCol+1 AND node.$rightCol AND current.$leftCol - node.$leftCol = 1")
                    ->order('node.' . $this->_left . ' DESC');

                break;
            case self::LAST_CHILD :
                $sql->where("current.$leftCol BETWEEN node.$leftCol+1 AND node.$rightCol AND node.$rightCol - current.$rightCol = 1")
                    ->order('node.' . $this->_left . ' DESC');

                break;
            case self::NEXT_SIBLING :
                $sql->where("current.$leftCol - node.$rightCol = 1");

                break;
            case self::PREV_SIBLING :
                $sql->where("node.$leftCol - current.$rightCol = 1");

                break;
        }

        $sql->where("current.$primary = ?", $nodeId, Zend_Db::INT_TYPE);

        $result = $this->_db->fetchRow($sql);
        if ($result) {
            return (int) $result[$this->_primary[1]];
        } else {
            return null;
        }
    }
}
