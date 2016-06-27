<?php

class Service_VotingReminder
{

    /**
     * @param string $confirmationHash
     * @throws \Dbjr_Mail_Exception
     * @throws \Zend_Db_Table_Exception
     * @throws \Zend_Exception
     */
    public function sendToVoter($confirmationHash)
    {
        $votesBatch = (new Model_Votes_Individual())->getByConfirmationHash($confirmationHash);

        if ($votesBatch !== null) {
            $mailService = new Service_Email();
            $mail = new Dbjr_Mail();
            $mail
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_PARTICIPANTS_REMINDER_VOTER)
                ->setPlaceholders([
                    'to_email' => $votesBatch['sub_user'],
                    'consultation_title_long' => $votesBatch['titl'],
                    'consultation_title_short' => $votesBatch['titl_short'],
                    'confirmation_url' => Zend_Registry::get('baseUrl') . '/voting/confirmvoting/kid/'
                        . $votesBatch['kid'] .'/hash/' . $votesBatch['confirmation_hash'],
                ])
                ->addTo($votesBatch['sub_user']);
            $mailService->queueForSend($mail);
            $mailService->sendQueued();
        }
    }

    /**
     * @param int $uid
     * @param string $subUid
     * @param int $consultationId
     * @throws \Dbjr_Mail_Exception
     * @throws \Zend_Exception
     */
    public function sendToGroupLeader($uid, $subUid, $consultationId)
    {
        $group = (new Model_Votes_Groups())->getWithDependencies($uid, $subUid, $consultationId);

        if ($group !== null && $group['sub_user'] !== $group['email']) {
            $mailService = new Service_Email();
            $groupAdminConfirmationUrl = Zend_Registry::get('baseUrl') . '/voting/confirmmember/kid/'
                .  $group['kid'] . '/authcode/' . $group['vt_code'] . '/user/' . $group['sub_uid'];

            $mail = new Dbjr_Mail();
            $mail
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_PARTICIPANTS_REMINDER_GROUP_ADMIN)
                ->setPlaceholders([
                    'to_name' => $group['name'] ? $group['name'] : $group['email'],
                    'to_email' => $group['email'],
                    'voter_email' => $group['sub_user'],
                    'consultation_title_long' => $group['titl'],
                    'consultation_title_short' => $group['titl_short'],
                    'confirmation_url' => $groupAdminConfirmationUrl . '/act/'
                        . md5($group['sub_user'] . $group['sub_uid'] . 'y'),
                    'rejection_url' => $groupAdminConfirmationUrl . '/act/'
                        . md5($group['sub_user'] . $group['sub_uid'] . 'n'),
                ])
                ->addTo($group['email']);
            $mailService->queueForSend($mail);
            $mailService->sendQueued();
        }
    }
}
