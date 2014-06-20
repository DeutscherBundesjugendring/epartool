<?php

class Dbjr_UrlkeyAction
{
    /**
     * Holds the variebles to be exported to the view
     * @var array
     */
    protected $_viewData;

    /**
     * Holds the name of the view script to be rendered relative to application/modules/default/views/scripts/urlkeyAction/
     * @var string
     */
    protected $_viewName;

    /**
     * The identifier of the urlkeyAction
     * @var integer
     */
    protected $_id;

    /**
     * The urlkey of this action
     * @var string
     */
    protected $_urlkey;

    /**
     * The output message of an action. [text => The message text., type => error|success|...]
     * @var array
     */
    protected $_message;


    /**
     * Creates an urlkeyAction
     * @param  array             $params The parameters to be attached to this action
     * @return Dbjr_UrlkeyAction         Fluent interface
     */
    public function create(array $params = array())
    {
        $this->generateUrlkey();
        $urlkeyActionModel = new Model_UrlkeyAction();
        $this->_id = $urlkeyActionModel->insert(
            [
                'urlkey' => $this->_urlkey,
                'time_created' => date('Y-m-d H:i:s'),
                'time_valid_to' => new Zend_Db_Expr(
                    $urlkeyActionModel->getAdapter()->quoteInto(
                        '(DATE_ADD(NOW(), INTERVAL ? MINUTE))',
                        Zend_Registry::get('systemconfig')->security->password->resetRequestTimeout
                    )
                ),
                'handler_class' => get_class($this),
            ]
        );

        foreach ($params as $name => $value) {
            (new Model_UrlkeyAction_Parameter())->insert(
                [
                    'urlkey_action_id' => $this->_id,
                    'name' => $name,
                    'value' => $value,
                ]
            );
        }

        return $this;
    }

    /**
     * Getter for $_viewData
     * @return array The variebles to be exported to the view object
     */
    public function getViewData()
    {
        return $this->_viewData;
    }

    /**
     * Getter for $_viewName
     * @return string|null The name of the view script. Can be null to imply redirect to homepage
     */
    public function getViewName()
    {
        return $this->_viewName;
    }

    /**
     * Getter for $_id
     * @return integer The identifier of the urlkeyAction
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Getter for $_message
     * @return array The output message of an action. [text => The message text., type => error|success|...]
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Getter for $_urlkey
     * @return string The urlkey
     */
    public function getUrlkey()
    {
        return $this->_urlkey;
    }

    /**
     * Generates an urlkey and sets to protected varieble
     * The string is guaranteed to be unique
     * @return Dbjr_UrlkeyAction  Fluent interface
     */
    public function generateUrlkey()
    {
        $urlkey = sha1(session_id() . microtime() . rand(0, 100));
        $urlkeyActionModel = new Model_UrlkeyAction();
        $row = $urlkeyActionModel->fetchRow(
            $urlkeyActionModel
                ->select()
                ->where('urlkey=?', $urlkey)
        );
        if ($row) {
            return $this->generateUrlkey();
        }
        $this->_urlkey = $urlkey;

        return $this;
    }

    /**
     * Marks this urlkeyAction as visited
     * @param  integer            $urlkeyActionId  Yhe urlkeyAction identifier
     * @return Dbjr_UrlkeyAction                   Fluent interface
     */
    protected function markVisited($urlkeyActionId)
    {
        (new Model_UrlkeyAction())->update(['time_visited' => date('Y-m-d H:i:s')], ['id=?' => $urlkeyActionId]);

        return $this;
    }
}
