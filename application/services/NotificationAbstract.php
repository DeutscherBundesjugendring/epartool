<?php

abstract class Service_NotificationAbstract
{
    const POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE = 'confirm_immediate';
    const POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST = 'confirm_email_request';

    /**
     * Method to be implemented by the varios subclasses that handles the actual sending of the notification
     * @param  array                        $params The params to be used during the notification
     * @return Service_NotificationAbstract         Fluent interface
        */
    abstract public function notify(array $params);

    /**
     * Subscribes user to receiving notification
     * @param  integer                       $userId      The identifier of the user
     * @param  array                         $params      Holds the notification subscription params
     * @param  string                        $postAction  Indicates whata ction to take after subscription. See self::POSTSUBSCRIBE_ACTION_*
     * @throws Dbjr_Notification_Exception                Throws is attempt is made to subscribe the same user twice with same params
     * @return Service_NotificationAbstract               Fluent interface
     */
    public function subscribeUser($userId, array $params, $postSubscribeAction)
    {
        $ntf = $this->getNotification($userId, $params);
        $postSubscribeFnc = function ($ntfId) use ($userId, $params, $postSubscribeAction) {
            if ($postSubscribeAction === self::POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE) {
                $this->confirm($ntfId);
            } elseif ($postSubscribeAction === self::POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST) {
                $this->sendConfirmationEmailRequest($userId, $ntfId, $params);
            }
        };

        if ($ntf && !$ntf->is_confirmed) {
            $postSubscribeFnc($ntf->id);
            return $this;
        } elseif ($ntf) {
            throw new Dbjr_Notification_Exception('Cant subscribe user to notification twice.');
        }

        $ntfId= (new Model_Notification())->insert(['user_id' => $userId, 'type_id' => $this->getTypeId()]);
        foreach ($params as $key => $value) {
            (new Model_Notification_Parameter())->insert(['name' => $key, 'notification_id' => $ntfId, 'value' => $value]);
        }

        $action = (new Service_UrlkeyAction_UnsubscribeNotification())->create(
            [Service_UrlkeyAction_ConfirmNotification::PARAM_NOTIFICATION_ID => $ntfId]
        );

        $postSubscribeFnc($ntfId);

        return $this;
    }

    /**
     * Unsubscribes user from receiving notification
     * @param  integer                       $userId  The identifier of the user
     * @param  array                         $params  Holds the notification subscription params
     * @return Service_NotificationAbstract           Fluent interface
     */
    public function unsubscribeUser($userId, array $params)
    {
        $ntfId = $this->getNotification($userId, $params)->id;
        $this->unsubscribeById($ntfId);

        return $this;
    }

    /**
     * Unsubscribes user from notification by notificationId
     * @param  integer                      $ntfId The notification identifier
     * @return Service_NotificationAbstract        Fluent interface
     */
    public function unsubscribeById($ntfId)
    {
        (new Model_Notification_Parameter())->delete(['notification_id=?' => $ntfId]);
        (new Model_Notification())->delete(['id=?' => $ntfId]);

        return $this;
    }

    /**
     * Confirms the notification subscription
     * @param  integer                       $ntfId   The notification identifier
     * @return Service_NotificationAbstract           Fluent interface
     */
    public function confirm($ntfId)
    {
        (new Model_Notification())->update(['is_confirmed' => true], ['id=?' => $ntfId]);

        return $this;
    }

    /**
     * Confirms the notification user
     * @param  integer                       $ntfId   The notification identifier
     * @return Service_NotificationAbstract           Fluent interface
     */
    public function confirmUser($ntfId)
    {
        $ntf = (new Model_Notification())->find($ntfId)->current();
        (new Model_Users())->update(['block' => 'c'], ['uid=?' => $ntf->user_id]);

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
                    'confirmation_url' =>  Zend_Registry::get('baseUrl') . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
                )
            )
            ->addTo($user->email);
        (new Service_Email)->queueForSend($mailer);

        return $this;
    }

    /**
     * Checks if a user is subscribed with the given params
     * @param  integer  $userId The identifier of the user
     * @param  array    $params The params that identify the notification along with type and userId
     * @return boolean          Indicates if such notification exists
     */
    public function isSubscribed($userId, array $params)
    {
        return (bool) $this->getNotification([$userId], $params);
    }

    /**
     * Returns the id of the notificationType of the concrete notification class
     * @return integer The identifier of the type
     */
    protected function getTypeId()
    {
        $ntfTypeModel = new Model_Notification_Type();
        $typeId = $ntfTypeModel->fetchRow(
            $ntfTypeModel
                ->select()
                ->where('name=?', static::TYPE_NAME)
        )->id;

        return $typeId;
    }

    /**
     * Returns notification id based on the userId an Params
     * @param  integer           $userId  The user identifier
     * @param  array             $params  The params of the subscription
     * @return Zend_Db_Table_Row          The notification object
     */
    protected function getNotification($userId, array $params)
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
            ->where('user_id=?', $userId)
            ->where('nt.name=?', static::TYPE_NAME)
            ->group('n.id');
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

        return $ntfModel->fetchRow($select);
    }
}
