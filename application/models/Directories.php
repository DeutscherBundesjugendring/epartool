<?php

class Model_Directories  extends NP_Db_Table_NestedSet
{
    protected $_name = 'dirs';
    protected $_primary = 'id';
    protected $_left = 'left';
    protected $_right = 'right';

    /**
     * Returns directory tree for the given question including number of inputs in each directory
     * @param  integer $qid The question identifier
     * @return array        An array of arrays with directory data
     */
    public function getByQuestion($qid)
    {
        $dirs = $this
            ->getTree(
                $this->getAdapter()->quoteInto('node.kid = (SELECT kid FROM quests WHERE qi=?)', $qid)
                . ' AND '
                . $this->getAdapter()->quoteInto('parent.kid = (SELECT kid FROM quests WHERE qi=?)', $qid)
            )
            ->toArray();

        foreach ($dirs as $key => &$dir) {
            $dir['count'] = (new Model_Inputs())->getNumByDirectory($qid, $dir['id']);
        }

        return $dirs;
    }
}
