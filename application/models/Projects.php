<?php

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

    public function getVideoServiceStatus(): bool
    {
        if ($this->videoServiceStatus === null) {
            $config = Zend_Registry::get('systemconfig');
            $project = (new Model_Projects())->find(($config->project))->current();
            $this->videoServiceStatus =
                (
                    $project['video_facebook_enabled']
                    && $config->webservice
                    && $config->webservice->facebook
                    && $config->webservice->facebook->appId
                    && $config->webservice->facebook->appSecret
                )
                || $project['video_youtube_enabled']
                || (
                    $project['video_vimeo_enabled']
                    && $config->webservice
                    && $config->webservice->vimeo
                    && $config->webservice->vimeo->accessToken);
        }

        return $this->videoServiceStatus;
    }
}
