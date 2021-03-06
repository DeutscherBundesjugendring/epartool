<?php

class Service_Notification_DiscussionContributionCreatedNotification extends Service_Notification_AbstractNotification
{
    const TYPE_NAME = 'input_discussion_contribution_created';
    const PARAM_INPUT_ID = 'input_id';

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
                ['inpt' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                'ntp.value = inpt.tid',
                ['tid']
            )
            ->join(
                ['quests' => (new Model_Questions())->info(Model_Questions::NAME)],
                'inpt.qi = quests.qi',
                ['q', 'qi', 'nr']
            )
            ->join(
                ['cnslt' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'quests.kid = cnslt.kid',
                ['titl', 'kid']
            )
            ->where('ntp.name=?', 'input_id')
            ->where('user_id=?', $userId)
            ->where('nt.name=?', static::TYPE_NAME)
            ->setIntegrityCheck(false)
            ->group('n.id');

        return $ntfModel->fetchAll($select);
    }

    /**
     * Notifies all users who have subscribed
     * @param  array                              $params  The params belonging to the current notification
     * @return Service_Notification_DiscussionContributionCreatedNotification
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
        $contribModel = new Model_InputDiscussion();
        $contrib = $contribModel->fetchRow(
            $contribModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['c' => $contribModel->info(Model_InputDiscussion::NAME)])
                ->join(
                    ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                    'i.tid = c.input_id',
                    []
                )
                ->join(
                    ['q' => (new Model_Questions())->info(Model_Inputs::NAME)],
                    'i.qi = q.qi',
                    ['kid']
                )
                ->where('c.input_id=?', $params[self::PARAM_INPUT_ID])
        );
        $urlkeys = $this->_getUrlkeys($ntfIds);
        foreach ($users as $user) {
            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_NOTIFICATION_NEW_INPUT_DISCUSSION_CONTRIB_CREATED)
                ->setPlaceholders([
                    'to_name' => $user->name ? $user->name : $user->email,
                    'to_email' => $user->email,
                    'website_url' => Zend_Registry::get('baseUrl')
                        . '/input/discussion/kid/' . $contrib->kid . '/inputId/' . $contrib->input_id,
                    'contribution_text' => $contrib->body,
                    'video_url' => !empty($contrib['video_service']) && !empty($contrib['video_id']) ?
                        sprintf(
                            Zend_Registry::get('systemconfig')->video->url->{$contrib['video_service']}
                                ->format->link,
                            $contrib['video_id']
                        ) : '',
                    'unsubscribe_url' => Zend_Registry::get('baseUrl')
                        . '/urlkey-action/execute/urlkey/' . $urlkeys[$user->notificationId],
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
            $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_DISCUSSION_SUBSCRIPTION_CONFIRMATION_NEW_USER;
        } else {
            $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_DISCUSSION_SUBSCRIPTION_CONFIRMATION;
        }

        $action = (new Service_UrlkeyAction_ConfirmNotification())->create(
            [Service_UrlkeyAction_ConfirmNotification::PARAM_NOTIFICATION_ID => $ntfId]
        );

        $input = (new Model_Inputs())->find($params[self::PARAM_INPUT_ID])->current();
        $mailer = new Dbjr_Mail();
        $mailer
            ->setTemplate($template)
            ->setPlaceholders([
                'to_name' => $user->name ? $user->name : $user->email,
                'to_email' => $user->email,
                'input_thes' => $input->thes,
                'input_expl' => $input->expl,
                'confirmation_url' =>  Zend_Registry::get('baseUrl')
                    . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
            ])
            ->addTo($user->email);
        (new Service_Email)
            ->queueForSend($mailer)
            ->sendQueued();

        return $this;
    }
}
