<?php
/**
 * Voting Settings 
 *
 * @description   Form of consultation
 * @author        Karsten Tackmann
 */
class Model_Votes_Settings extends Model_DbjrBase {

	protected $_name = 'vt_settings';
	protected $_primary = 'cnslt_id';
	
	public function add($consultationID) {
		$data = array('cnslt_id' => $consultationID);
		return (int)$this -> insert($data);
	}
	public function getById($id) {
		
			$result = array();
			$row = $this -> find($id) -> current();
			if ($row) {
				$result = $row -> toArray();
			}
		return $result;
	}

}
?>