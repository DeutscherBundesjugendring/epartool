<?php

/**
 * Description of Model Followups
 *
 * @author Marco Dinnbier
 */
class Model_Followups extends Zend_Db_Table_Abstract {
    protected $_name = 'fowups';
    protected $_primary = 'fid';

    protected $_dependentTables = array('Model_FollowupsRef','Model_FollowupsSupports');

    protected $_referenceMap = array(
      'FollowupFiles' => array(
        'columns' => 'ffid', 'refTableClass' => 'Model_FollowupFiles', 'refColumns' => 'ffid'
      )
    );
  
    /**
    * getFollowupsbyInput
    * get followup-files by inpt.tid
    * @param integer $tid
    * @return Zend_DB_Table_Rowset
    */
    public function getByInput($tid) {

        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($tid)) {
          return array();
        }
        $result = array();
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(array('fr' => 'fowups_rid'));
        $select->joinLeft(array('f' => 'fowups'), 'fr.fid_ref = f.fid');
        $select->where('fr.tid=?', $tid);


        $stmt = $db->query($select);
        $rowSet = $stmt->fetchAll();

        if($rowSet) {
          $result = $rowSet;
        }

        return $result;


    }
    /**
    * getRelated
    * get related fowups/fowup_fls by fowups.fid
    * @param integer $id
    * @return array
    */
     public function getRelated($id, $where = NULL) {
         
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
        $result['docs'] = array();
        $result['count'] = 0;
        $row = $this->find($id)->current(); 
        if($row){
            
            $Model_Inputs = new Model_Inputs();
            $Model_FollowupFiles = new Model_FollowupFiles();
            
            $rowset = $row->findDependentRowset($depTable, NULL, $depTableSelect );

            $refs = $rowset->toArray();
            
            $inputs = array();
            $snippets = array();
            $docs = array();

            foreach ($refs as $ref) {

                if ($ref['tid']) $inputs[] = $ref['tid'];
                if ($ref['fid']) $snippets[] = $ref['fid'];
                if ($ref['ffid']) $docs[] = $ref['ffid'];

            }
            
           
            $result['inputs'] = $Model_Inputs->find($inputs)->toArray();
            $result['snippets'] = $this->find($snippets)->toArray();
            $result['docs'] = $Model_FollowupFiles->find($docs)->toArray();
            $result['count'] = count($refs);
            
        }

        return $result; 
         
     }
     
     
  
    
    /**
    * getById
    * get followup by fowups.fid
    * @param integer $fid
    * @return array 
    */
    public function getById($id) {
       
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
    * get followup by fowups.fid array
    * @param array $idarray
    * @return array 
    */
    public function getByIdArray( $idarray ) {

  
         if (count($idarray) == 0) {
          
          return array();
          
      }
        $result = array();
        $select = $this->select();
        $select->where('fid IN(?)', $idarray);
        
        $result = $this->fetchAll($select)->toArray();

        return $result;
        
    }
    
    /**
    * getById
    * delete followup by fowups.fid
    * @param integer $fid
    */
    public function deleteById($fid) {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($fid)) {
          return 0;
        }
        // exists?
        if ($this->find($fid)->count() < 1) {
          return 0;
        }    

        // where
        $followup = $this->find($fid)->current();
        $result = $followup->delete();
       /* $where = $this->getDefaultAdapter()
            ->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);*/
        return $result;
    }
    
    /**
    * supportById
    * increment fowups.lkyea/fowups.lknay by fowups.fid if not liked by useragent+ip
    * @param integer $fid
    * @param string $field ['lkyea' OR 'lknay']
    * @return integer count($field) 
    */
    public function supportById( $fid, $field ) {

          $validator = new Zend_Validate_Int();
          if (!$validator->isValid($fid)) {
              return 0;
          }

          $userAgent = new Zend_Http_UserAgent;
          $tmphash = md5($userAgent->getDevice()->getUserAgent() . getenv($_SERVER['REMOTE_ADDR']));
          
          if ($this->find($fid)->count() < 1) {
            return 0;
          }
          
          $followup = $this->find($fid)->current();
          $count = $followup[$field];

          $followupSupports = new Model_FollowupsSupports;
          $isLiked = $followupSupports->find($fid, $tmphash)->current();

          if (!$isLiked ) {

                  $followupSupportsRow = $followupSupports->createRow();
                  $followupSupportsRow->fid = $fid;
                  $followupSupportsRow->tmphash = $tmphash;
                  $followupSupportsRow->save();

                  $followup = $this->find($fid)->current();
                  $count = $followup[$field] + 1;
                  $followup[$field] = $count;
                  $followup->save();                
          };

          return (int)$count;
    }
}

?>
