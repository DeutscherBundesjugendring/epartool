<?php

class Service_UrlkeyAction_ConfirmInputDiscussionContribution extends Service_UrlkeyAction
{
    const PARAM_DISCUSSION_CONTRIBUTION_ID = 'discussion_contribution_id';
    const NAME = 'confirmInputDiscussionContribution';

    /**
     * Holds the name of the view script to be used for this action.
     * If null, the urlkeyActionController will redirect to home
     * @var string
     */
    protected $_viewName = 'confirmInputDiscussionContribution';

    /**
     * Executes this urlkeyAction
     * @param  Zend_Controller_Request_Http        $request      The request object
     * @param  Zend_Db_Table_Row                   $urlkeyAction The urlkeyAction object
     * @return Service_UrlkeyAction_ResetPassword                Fluent interface
     */
    public function execute(Zend_Controller_Request_Http $request, Zend_Db_Table_Row $urlkeyAction)
    {
        $urlkeyActionParamModel = new Model_UrlkeyAction_Parameter();
        $contribId = $urlkeyActionParamModel->fetchRow(
            $urlkeyActionParamModel
                ->select()
                ->where('urlkey_action_id=?', $urlkeyAction->id)
                ->where('name=?', self::PARAM_DISCUSSION_CONTRIBUTION_ID)
        )->value;
        $contrib = (new Model_InputDiscussion())->find($contribId)->current();

        $this->_viewData['form'] = new Default_Form_UrlkeyAction_ConfirmInputDiscussionContribution();
        if ($request->isPost()) {
            if ($this->_viewData['form']->isValid($request->getPost())) {

                (new Model_InputDiscussion())->update(['is_user_confirmed' => 1], ['id=?' => $contribId]);
                (new Model_Users())->update(['block' => 'c'], ['uid=?' => $contrib->user_id]);

                $this->_viewName = null;
                $this->_message = ['text' => 'Your discussion contribution was confirmed.', 'type' => 'success'];
                $this->markVisited($urlkeyAction->id);
            } else {
                $this->_message = ['text' => 'Form invalid.', 'type' => 'error'];
            }
        }

        $this->_viewData['discussionContrib'] = $contrib;

        return $this;
    }
}
