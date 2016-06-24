<?php
/**
 * Projects
 * @author Markus Hackel
 *
 */
class Model_Projects extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'proj';
    protected $_primary = 'proj';

    /**
     * @var bool
     */
    protected $videoServiceStatus;
    
    /**
     * Returns all entries from the proj table
     *
     * @param  string                        $order [optional]
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAll($order = '')
    {
        $select = $this->select();
        if (!empty($order)) {
            $select->order($order);
        }

        return $this->fetchAll($select);
    }
    
    /**
     * @return bool
     */
    public function getVideoServiceStatus()
    {
        if ($this->videoServiceStatus === null) {
            $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();
            $this->videoServiceStatus = $project['video_facebook_enabled'] || $project['video_youtube_enabled']
            || $project['video_vimeo_enabled'];
        }
        
        return $this->videoServiceStatus;
    }
}
