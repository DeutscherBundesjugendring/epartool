<?php

class Service_Notification_FollowUpCreatedNotification extends Service_Notification_AbstractNotification
{
    const TYPE_NAME = 'follow_up_created';
    const PARAM_CONSULTATION_ID = 'kid';

    /**
     * @param int $userId
     * @return \Zend_Db_Table_Rowset_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    public function getNotifications($userId)
    {
        $ntfModel = new Model_Notification();
        $select = $ntfModel
            ->select()
            ->setIntegrityCheck(false)
            ->from(['n' => $ntfModel->info(Model_Notification::NAME)])
            ->join(
                ['nt' => (new Model_Notification_Type())->info(Model_Notification_Type::NAME)],
                'n.type_id = nt.id',
                []
            )
            ->join(
                ['ntp' => (new Model_Notification_Parameter())->info(Model_Notification_Parameter::NAME)],
                'n.id = ntp.notification_id',
                []
            )
            ->join(
                ['fowup_fls' => (new Model_FollowupFiles())->info(Model_FollowupFiles::NAME)],
                'ntp.value = fowup_fls.kid',
                ['ffid_titl' => 'fowup_fls.titl']
            )
            ->join(
                ['cnslt' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'ntp.value = cnslt.kid',
                ['kid']
            )
            ->where('ntp.name=?', self::PARAM_CONSULTATION_ID)
            ->where('user_id=?', $userId)
            ->where('nt.name=?', static::TYPE_NAME)
            ->group('n.id');

        return $ntfModel->fetchAll($select);
    }

    /**
     * Notifies all users who have subscribed
     * @param  array                              $params  The params belonging to the current notification
     * @return Service_Notification_FollowUpCreatedNotification          Fluent interface
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
        $consultation = $this->getConsultation($params);
        $urlKeys = $this->_getUrlkeys($ntfIds);
        foreach ($users as $user) {
            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_NOTIFICATION_NEW_FOLLOW_UP_FILE_CREATED)
                ->setPlaceholders([
                    'to_name' => $user->name ? $user->name : $user->email,
                    'to_email' => $user->email,
                    'website_url' => Zend_Registry::get('baseUrl') . '/followup/index/kid/' . $consultation->kid,
                    'consultation_title_long' => $consultation->titl,
                    'unsubscribe_url' => Zend_Registry::get('baseUrl')
                        . '/urlkey-action/execute/urlkey/' . $urlKeys[$user->notificationId],
                ])
                ->addTo($user->email);
            (new Service_Email)
                ->queueForSend($mailer)
                ->sendQueued();
        }

        return $this;
    }

    /**
     * Sends confirmation requests to the users
     * @param  integer                       $userId   The user identifier
     * @param  integer                       $ntfId    The notification identifier
     * @param  array                         $params   The params belongign to this notification
     * @return Service_Notification_AbstractNotification            Fluent interface
     */
    protected function sendConfirmationEmailRequest($userId, $ntfId, array $params)
    {
        $user = (new Model_Users())->find($userId)->current();
        if ($user->is_confirmed !== null) {
            $template = Model_Mail_Template::SYSTEM_TEMPLATE_FOLLOW_UP_SUBSCRIPTION_CONFIRMATION_NEW_USER;
        } else {
            $template = Model_Mail_Template::SYSTEM_TEMPLATE_FOLLOW_UP_SUBSCRIPTION_CONFIRMATION;
        }

        $action = (new Service_UrlkeyAction_ConfirmNotification())->create(
            [Service_UrlkeyAction_ConfirmNotification::PARAM_NOTIFICATION_ID => $ntfId]
        );

        $mailer = new Dbjr_Mail();
        $mailer
            ->setTemplate($template)
            ->setPlaceholders([
                'to_name' => $user->name ? $user->name : $user->email,
                'to_email' => $user->email,
                'consultation_title_long' => $this->getConsultation($params)->titl,
                'confirmation_url' =>  Zend_Registry::get('baseUrl')
                    . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
            ])
            ->addTo($user->email);
        (new Service_Email)
            ->queueForSend($mailer)
            ->sendQueued();

        return $this;
    }

    /**
     * @param array $params
     * @return \Zend_Db_Table_Row_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    private function getConsultation(array $params) {
        return (new Model_Consultations())->find($params[self::PARAM_CONSULTATION_ID])->current();
    }
}
