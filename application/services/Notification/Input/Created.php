<?php

class Service_Notification_Input_Created extends Service_NotificationAbstract
{
    const TYPE_NAME = 'input_created';
    const PARAM_QUESTION_ID = 'question_id';

    /**
     * Notifies all users who have subscribed
     * @param  array                              $params  The params belonging to the current notification
     * @return Service_Notification_Input_Created          Fluent interface
     */
    public function notify(array $params)
    {
        $users = $this->_getRecipients($params);
        if (count($users) === 0) {
            return $this;
        }

        $ntfIds = [];
        foreach ($users as $user) {
            $ntfIds[] = $user->notificationId;
        }
        $question = (new Model_Questions())->find($params[self::PARAM_QUESTION_ID])->current();
        $urlkeys = $this->_getUrlkeys($ntfIds);
        foreach ($users as $user) {
            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_NOTIFICATION_NEW_INPUT_CREATED)
                ->setPlaceholders(
                    array(
                        'to_name' => $user->name ? $user->name : $user->email,
                        'to_email' => $user->email,
                        'website_url' => Zend_Registry::get('baseUrl') . '/input/show/kid/' . $question->kid . '/qid/' . $question->qi,
                        'question_text' => $question->q,
                        'unsubscribe_url' => Zend_Registry::get('baseUrl') . '/urlkey-action/execute/urlkey/' . $urlkeys[$user->notificationId],
                    )
                )
                ->addTo($user->email);
            (new Service_Email)->queueForSend($mailer);
        }

        return $this;
    }

    /**
     * Sends confirmation requests to the users
     * @param  integer                       $userId   The user identifier
     * @param  integer                       $ntfId    The notification identifier
     * @param  array                         $params   The params belongign to this notification
     * @return Service_NotificationAbstract            Fluent interface
     */
    protected function sendConfirmationEmailRequest($userId, $ntfId, array $params)
    {
        $user = (new Model_Users())->find($userId)->current();
        if ($user->block !== 'u') {
            $template = Model_Mail_Template::SYSTEM_TEMPLATE_SUBSCRIPTION_CONFIRMATION_NEW_USER;
        } else {
            $template = Model_Mail_Template::SYSTEM_TEMPLATE_SUBSCRIPTION_CONFIRMATION;
        }

        $action = (new Service_UrlkeyAction_ConfirmNotification())->create(
            [Service_UrlkeyAction_ConfirmNotification::PARAM_NOTIFICATION_ID => $ntfId]
        );

        $mailer = new Dbjr_Mail();
        $mailer
            ->setTemplate($template)
            ->setPlaceholders(
                array(
                    'to_name' => $user->name ? $user->name : $user->email,
                    'to_email' => $user->email,
                    'question_text' => (new Model_Questions())->find($params[self::PARAM_QUESTION_ID])->current()->q,
                    'confirmation_url' =>  Zend_Registry::get('baseUrl') . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
                )
            )
            ->addTo($user->email);
        (new Service_Email)->queueForSend($mailer);

        return $this;
    }

    /**
     * Returns recipients along with their notification identifiers
     * @param  array                 $params The params belonging to the current notification
     * @return Zend_Db_Table_Rowset          The user objects with notificationId value set
     */
    private function _getRecipients($params)
    {
        $userModel = new Model_Users();
        $select = $userModel
            ->select()
            ->setIntegrityCheck(false)
            ->from(['u' => $userModel->info(Model_Users::NAME)])
            ->join(
                ['n' => ( new Model_Notification())->info(Model_Notification::NAME)],
                'n.user_id = u.uid',
                ['notificationId' => 'id']
            )
            ->join(
                ['nt' => (new Model_Notification_Type())->info(Model_Notification_Type::NAME)],
                'n.type_id = nt.id',
                []
            )
            ->where('u.block=?', 'c')
            ->where('n.is_confirmed=?', 1)
            ->where('nt.name=?', static::TYPE_NAME)
            ->group('u.uid');
        foreach ($params as $key => $value) {
            $tblRef = 'p' . $key;
            $select
                ->join(
                    [$tblRef => (new Model_Notification_Parameter())->info(Model_Notification_Parameter::NAME)],
                    $tblRef . '.notification_id = n.id',
                    []
                )
                ->where($tblRef . '.name=?', $key)
                ->where($tblRef . '.value=?', $value);
        }

        return $userModel->fetchAll($select);
    }

    /**
     * Returns urlkeys by notificationId
     * @param  array $ntfIds Array holding the notification identifiers
     * @return array         An array on format[$notificationId => $urlkey]
     */
    private function _getUrlkeys(array $ntfIds)
    {
        $ukaModel = new Model_UrlkeyAction();
        $urlkeysRaw = $ukaModel->fetchAll(
            $ukaModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['ua' => $ukaModel->info(Model_UrlkeyAction::NAME)], ['urlkey'])
                ->join(
                    ['uap' => (new Model_UrlkeyAction_Parameter())->info(Model_UrlkeyAction_Parameter::NAME)],
                    'ua.id = uap.urlkey_action_id',
                    ['notificationId' => 'uap.value']
                )
                ->where('uap.name=?', Service_UrlkeyAction_UnsubscribeNotification::PARAM_NOTIFICATION_ID)
                ->where('uap.value IN (?)', $ntfIds)
                ->where('ua.handler_class=?', get_class(new Service_UrlkeyAction_UnsubscribeNotification()))
                ->group('ua.urlkey')
        );
        $urlkeys = [];
        foreach ($urlkeysRaw as $urlkey) {
            $urlkeys[$urlkey->notificationId] = $urlkey->urlkey;
        }

        return $urlkeys;
    }
}
