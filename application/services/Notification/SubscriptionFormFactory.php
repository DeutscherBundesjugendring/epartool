<?php

class Service_Notification_SubscriptionFormFactory
{
    /**
     * @param \Service_Notification_AbstractNotification $notification
     * @param array $params
     * @return \Default_Form_SubscribeNotification|\Default_Form_UnsubscribeNotification
     */
    public function getForm(Service_Notification_AbstractNotification $notification, array $params)
    {
        $auth = Zend_Auth::getInstance();

        $isSubscribed = false;
        if ($auth->hasIdentity()) {
            $isSubscribed = $notification->isSubscribed($auth->getIdentity()->uid, $params);
        }

        if ($isSubscribed) {
            $form = new Default_Form_UnsubscribeNotification();
        } else {
            $form = new Default_Form_SubscribeNotification();
            if (!$auth->hasIdentity()) {
                $form->requireId();
            }
        }

        return $form;
    }
}
