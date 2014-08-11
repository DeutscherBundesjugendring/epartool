<?php

class Service_UrlkeyAction_UnsubscribeNotification extends Service_UrlkeyAction
{
    const PARAM_NOTIFICATION_ID = 'notification_id';

    /**
     * Holds the name of the view script to be used for this action.
     * If null, the urlkeyActionController will redirect to home
     * @var string
     */
    protected $_viewName = 'unsubscribeNotification';

    /**
     * Executes this urlkeyAction
     * @param  Zend_Controller_Request_Http     $request      The request object
     * @param  Zend_Db_Table_Row                $urlkeyAction The urlkeyAction object
     * @return Service_UrlkeyAction_ResetPassword                Fluent interface
     */
    public function execute(Zend_Controller_Request_Http $request, Zend_Db_Table_Row $urlkeyAction)
    {
        $this->_viewData['form'] = new Default_Form_UrlkeyAction_UnsubscribeNotification();
        if ($request->isPost()) {
            if ($this->_viewData['form']->isValid($request->getPost())) {
                $urlkeyActionParamModel = new Model_UrlkeyAction_Parameter();
                $ntfId = $urlkeyActionParamModel->fetchRow(
                    $urlkeyActionParamModel
                        ->select()
                        ->where('urlkey_action_id=?', $urlkeyAction->id)
                        ->where('name=?', self::PARAM_NOTIFICATION_ID)
                )->value;
                (new Service_Notification_Input_Created())->unsubscribeById($ntfId);
                $this->_viewName = null;
                $this->_message = ['text' => 'Your subscription was canceled.', 'type' => 'success'];
                $this->markVisited($urlkeyAction->id);
            } else {
                $this->_message = ['text' => 'Form invalid.', 'type' => 'error'];
            }
        }

        return $this;
    }
}
