<?php

class Model_Directories extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'dirs';
    protected $_primary = 'id';

    /**
     * Returns directories for the given question including number of inputs in each directory
     * @param  integer $qid The question identifier
     * @return array        An array of arrays with directory data
     */
    public function getByQuestion($qid)
    {
        $dirs = $this->fetchAll(
            $this
                ->select()
                ->setIntegrityCheck(false)
                ->from(['d' => $this->info(self::NAME)])
                ->join(
                    ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                    'q.kid = d.kid',
                    []
                )
                ->where('q.qi = ?', $qid)
                ->order(new Zend_Db_Expr('ISNULL(`order`), `order`'))
        )
        ->toArray();

        foreach ($dirs as $key => &$dir) {
            $dir['count'] = (new Model_Inputs())->getNumByDirectory($qid, $dir['id']);
        }

        return $dirs;
    }

    /**
     * Deletes a directory. If there are any inputs within it, thet are removed from it first
     * @see  Zend_Db_Table_Abstract::delete()
     * @param  array|string $where SQL WHERE clause(s).
     * @return integer             The number of rows deleted.
     */
    public function delete($where)
    {
        $res = $this->fetchAll($where);

        $dirIds = [];
        foreach ($res as $dir) {
            $dirIds[] = $dir->id;
        }
        (new Model_Inputs())->update(['dir' => 0], ['dir IN (?)' => $dirIds]);

        return parent::delete($where);
    }
}
