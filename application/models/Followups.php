<?php

class Model_Followups extends Zend_Db_Table_Abstract
{
    const TYPE_GENERAL = 'general';
    const TYPE_SUPPORTING = 'supporting';
    const TYPE_ACTION = 'action';
    const TYPE_REJECTION = 'rejected';
    const TYPE_END = 'end';

    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    protected $_name = 'fowups';
    protected $_primary = 'fid';

    protected $_dependentTables = array('Model_FollowupsRef','Model_FollowupsSupports');

    protected $_referenceMap = array(
      'FollowupFiles' => array(
        'columns' => 'ffid', 'refTableClass' => 'Model_FollowupFiles', 'refColumns' => 'ffid'
      )
    );

    public static function getTypes() {
        $translator = Zend_Registry::get('Zend_Translate');
        return [
            self::TYPE_GENERAL => $translator->translate('General'),
            self::TYPE_SUPPORTING => $translator->translate('Supporting'),
            self::TYPE_ACTION => $translator->translate('Action'),
            self::TYPE_REJECTION => $translator->translate('Rejected'),
            self::TYPE_END => $translator->translate('End'),
        ];
    }

    public function getByKid($kid, $order = NULL, $limit = NULL)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            return array();
        }
        $select = $this->select();
        $select->where('kid=?', $kid);

        if ($order) {
            $select->order($order);
        }
        if ($limit) {

            $select->limit($limit);
        }
        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    /**
     * @param $tid
     * @return array
     */
    public function getByInput($tid)
    {
        $db = $this->getAdapter();
        $select = $db->select()
            ->from(['fr' => 'fowups_rid'])
            ->joinLeft(['f' => 'fowups'], 'fr.fid_ref = f.fid')
            ->where('fr.tid=?', $tid);

        return $db->query($select)->fetchAll();

    }

    /**
    * getRelated
    * get related fowups/fowup_fls by fowups.fid
    * @param integer $id
    * @return array
    */
     public function getRelated($id, $where = NULL)
     {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
          return array();
        }

        $depTable = new Model_FollowupsRef();
        $depTableSelect = $depTable->select();
        if ($where) {
            $depTableSelect->where($where);
        }

        $result = array();
        $result['inputs'] = array();
        $result['snippets'] = array();
        $result['followups'] = array();
        $result['count'] = 0;
        $row = $this->find($id)->current();
        if ($row) {

            $modelInputs = new Model_Inputs();
            $modelFollowupFiles = new Model_FollowupFiles();

            $rowset = $row->findDependentRowset($depTable, NULL, $depTableSelect);

            $refs = $rowset->toArray();

            $inputs = array();
            $snippets = array();
            $docs = array();

            foreach ($refs as $ref) {

                if ($ref['tid']) $inputs[] = $ref['tid'];
                if ($ref['fid']) $snippets[] = $ref['fid'];
                if ($ref['ffid']) $docs[] = $ref['ffid'];

            }

            $result['inputs'] = $modelInputs->find($inputs)->toArray();
            $result['snippets'] = $this->find($snippets)->toArray();
            $result['followups'] = $modelFollowupFiles->find($docs)->toArray();
            $result['count'] = count($refs);

        }

        return $result;
     }

    /**
     * @param int $id
     * @return int
     */
     public function getRelatedCount($id)
     {
         $followupsRef = new Model_FollowupsRef();
         $select = $followupsRef->select()
             ->from(['f' => $followupsRef->info(self::NAME)], [new Zend_Db_Expr('COUNT(*) as count')])
             ->where('f.fid_ref = ?', (int) $id);

         return (int) $followupsRef->fetchAll($select)->current()->count;
     }

    /**
    * getById
    * get reaction_file by fowups.fid
    * @param int $id
    * @return array
    */
    public function getById($id)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
          return array();
        }
        $result = array();
        $row = $this->find($id)->current();
        if ($row) {
          $result = $row->toArray();
        }

        return $result;
    }

    /**
    * getByIdArray
    * get reaction_file by fowups.fid array
    * @param array $idarray
    * @return array
    */
    public function getByIdArray($idarray)
    {
        if (!is_array($idarray) || count($idarray) == 0) {
            return array();
        }
        $result = array();
        $select = $this->select();
        $select->where('fid IN(?)', $idarray);

        $result = $this->fetchAll($select)->toArray();

        return $result;

    }

    /**
    * supportById
    * increment fowups.lkyea/fowups.lknay by fowups.fid if not liked by useragent+ip
    * @param integer $fid
    * @param string $field ['lkyea' OR 'lknay']
    * @return integer count($field)
    */
    public function supportById($fid, $field)
    {
          $validator = new Zend_Validate_Int();
          if (!$validator->isValid($fid)) {
              return 0;
          }

          $userAgent = new Zend_Http_UserAgent;
          $tmphash = md5($userAgent->getDevice()->getUserAgent() . getenv($_SERVER['REMOTE_ADDR']));

          if ($this->find($fid)->count() < 1) {
            return 0;
          }

          $snippet = $this->find($fid)->current();
          $count = $snippet[$field];

          $modelFollowupsSupports = new Model_FollowupsSupports;
          $isLiked = $modelFollowupsSupports->find($fid, $tmphash)->current();

          if (!$isLiked) {

                  $followupSupportsRow = $modelFollowupsSupports->createRow();
                  $followupSupportsRow->fid = $fid;
                  $followupSupportsRow->tmphash = $tmphash;
                  try {
                      $followupSupportsRow->save();
                  } catch (Zend_Db_Statement_Exception $e) {
                      if ($e->getCode() !== self::ERROR_CODE_DUPLICATE_ENTRY) {
                          throw $e;
                      }

                      return (int) $count;
                  }


                  $snippet = $this->find($fid)->current();
                  $count = $snippet[$field] + 1;
                  $snippet[$field] = $count;
                  $snippet->save();
          };

          return (int) $count;
    }

    /**
     * Search in reaction_snippets
     * @param string  $needle   The term being searchd for
     * @return array            An array of reaction_files with reaction_snippets
     */
    public function search($needle)
    {
        $result = array();
        if ($needle !== '') {
            $result = $this
                ->getAdapter()
                ->select()
                ->from(
                    array('f' => 'fowups')
                )
                ->join(
                    array('ff' => 'fowup_fls'),
                    'f.ffid = ff.ffid',
                    array('titl', 'who', 'kid', 'ffid', 'is_only_month_year_showed', 'ref_doc', 'when', 'gfx_who')
                )
                ->where('LOWER(expl) LIKE ?', '%' . $needle . '%')
                ->order(array('ff.when', 'f.docorg ASC'))
                ->query()
                ->fetchAll();
        }

        $followUps = array();
        foreach ($result as $followUp) {
            if (isset($followUps[$followUp['ffid']])) {
                $followUps[$followUp['ffid']]['snippets'][] = array(
                    'text' => $followUp['expl'],
                );
            } else {
                $followUps[$followUp['ffid']] = array(
                    'title' => $followUp['titl'],
                    'releasedBy' => $followUp['who'],
                    'timeReleased' => $followUp['when'],
                    'isOnlyMonthYearShowed' => $followUp['is_only_month_year_showed'],
                    'filename' => $followUp['ref_doc'],
                    'filenameThumb' => $followUp['gfx_who'],
                    'consultationId' => $followUp['kid'],
                    'snippets' => array(
                        array(
                            'text' => $followUp['expl'],
                        ),
                    ),
                );
            }
        }

        return $followUps;
    }

    /**
     * @param int $id
     * @return int
     */
    public function getConsultationIdBySnippet($id)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['f' => $this->info(self::NAME)], [])
            ->joinLeft(['d' => (new Model_FollowupFiles)->info(self::NAME)], 'd.ffid = f.ffid', ['kid'])
            ->where('f.fid = ?', (int) $id);

        $snippet = $this->fetchAll($select)->current();
        if (!$snippet || !isset($snippet['kid'])) {
            return -1;
        }

        return (int) $snippet['kid'];
    }
}
