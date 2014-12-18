<?php

/**
 * Class Admin_SettingsController
 */

class Admin_SettingsController extends Zend_Controller_Action
{
    /**
     * FlashMessenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger = null;

    /**
     * @desc Construct
     * @return void
     */
    public function init()
    {
        // Setzen des Standardlayouts
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger =
            $this->_helper->getHelper('FlashMessenger');
        $this->initView();
    }

    /**
     * @desc media dashboard
     * @return void
     */
    public function indexAction()
    {

    }
}
