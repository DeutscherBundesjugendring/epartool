<?php

class Model_Followups extends Zend_Db_Table_Abstract
{
    protected $_name = 'fowups';
    protected $_primary = 'fid';

    protected $_dependentTables = array('Model_FollowupsRef','Model_FollowupsSupports');

    protected $_referenceMap = array(
      'FollowupFiles' => array(
        'columns' => 'ffid', 'refTableClass' => 'Model_FollowupFiles', 'refColumns' => 'ffid'
      )
    );

    private static $_types = [
        'g' => 'general',
        's' => 'supporting',
        'a' => 'action',
        'r' => 'rejected',
        'e' => 'end'
    ];

    private static $_hierarchyLevels = [
        "Fußnote",
        "Fließtext",
        "Überschrift 1",
        "Überschrift 2",
        "Überschrift 3",
        "Überschrift 4",
        "Überschrift 5",
    ];

    public static function getTypes() {
        return self::$_types;
    }

    public static function getHierarchyLevels() {
        return self::$_hierarchyLevels;
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
    * getById
    * get follow-up by fowups.fid
    * @param integer $fid
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
    * get follow-up by fowups.fid array
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
                  $followupSupportsRow->save();

                  $snippet = $this->find($fid)->current();
                  $count = $snippet[$field] + 1;
                  $snippet[$field] = $count;
                  $snippet->save();
          };

          return (int) $count;
    }

    /**
     * Search in follow-up snippets
     * @param string  $needle   The term being searchd for
     * @return array            An array of follow-ups with snippets
     */
    public function search($needle)
    {
        $needle = htmlentities($needle);
        $result = array();
        if ($needle !== '') {
            $result = $this
                ->getAdapter()
                ->select()
                ->from(
                    array('f' => 'fowups'),
                    array('expl', 'hlvl')
                )
                ->join(
                    array('ff' => 'fowup_fls'),
                    'f.ffid = ff.ffid',
                    array('titl', 'who', 'kid', 'ffid', 'show_no_day', 'ref_doc', 'when', 'gfx_who')
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
                    'hierarchyLevel' => $followUp['hlvl'],
                );
            } else {
                $followUps[$followUp['ffid']] = array(
                    'title' => $followUp['titl'],
                    'releasedBy' => $followUp['who'],
                    'timeReleased' => $followUp['when'],
                    'showNoDay' => $followUp['show_no_day'],
                    'filename' => $followUp['ref_doc'],
                    'filenameThumb' => $followUp['gfx_who'],
                    'consultationId' => $followUp['kid'],
                    'snippets' => array(
                        array(
                            'text' => $followUp['expl'],
                            'hierarchyLevel' => $followUp['hlvl'],
                        ),
                    ),
                );
            }
        }

        return $followUps;
    }
}
