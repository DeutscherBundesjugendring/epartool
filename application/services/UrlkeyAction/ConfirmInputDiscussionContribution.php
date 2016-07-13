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
        $discussionContribution = (new Model_InputDiscussion())->find($contribId)->current();
        $contribution = (new Model_Inputs())->find($discussionContribution['input_id'])->current();
        $question = (new Model_Questions())->find($contribution['qi'])->current();
        $consultation = (new Model_Consultations())->find($question['kid'])->current();
        $this->_viewData['contribution'] = $contribution;
        $this->_viewData['question'] = $question;
        $this->_viewData['consultation'] = $consultation;
        $this->_viewData['videoServicesStatus'] = (new Model_Projects())->find(
            Zend_Registry::get('systemconfig')->project
        )->current();
        $this->_viewData['form'] = new Default_Form_UrlkeyAction_ConfirmInputDiscussionContribution();
        if ($request->isPost()) {
            $translator = Zend_Registry::get('Zend_Translate');
            $data = $request->getPost();
            if ($this->_viewData['form']->isValid($data)) {
                if (isset($data['confirm'])) {
                    (new Model_InputDiscussion())->update(['is_user_confirmed' => 1], ['id=?' => $contribId]);
                    (new Model_Users())->update(['block' => 'c'], ['uid=?' => $discussionContribution->user_id]);

                    $this->_viewName = null;
                    $this->_message = [
                        'text' => $translator->translate('Your discussion post was confirmed.'),
                        'type' => 'success',
                    ];
                    $this->markVisited($urlkeyAction->id);
                    $this->_redirectUrl = '/input/discussion/kid/' . $question['kid'] . '/inputId/' . $contribution['tid'];
                    (new Service_Notification_DiscussionContributionCreatedNotification())->notify(
                        [Service_Notification_DiscussionContributionCreatedNotification::PARAM_INPUT_ID => $discussionContribution->input_id]
                    );
                } elseif (isset($data['delete'])) {
                    (new Model_InputDiscussion())->delete(['id=?' => $contribId]);

                    $this->_viewName = null;
                    $this->_message = [
                        'text' => $translator->translate('Your discussion post was deleted.'),
                        'type' => 'success',
                    ];
                    $this->markVisited($urlkeyAction->id);
                    $this->_redirectUrl = '/input/discussion/kid/' . $question['kid'] . '/inputId/' . $contribution['tid'];
                }
            } else {
                $this->_message = ['text' => $translator->translate('Form invalid.'), 'type' => 'error'];
            }
        }

        $this->_viewData['discussionContribution'] = $discussionContribution;

        return $this;
    }
}
