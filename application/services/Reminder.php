<?php

class Service_Reminder
{
    /**
     * @param int $consultationId
     * @param int $uid
     * @param string $subUid
     */
    public function send($consultationId, $uid, $subUid)
    {
        $user = (new Model_Users())->find($uid)->current();
        $consultation = (new Model_Consultations())->find($consultationId)->current();
        $votingRights = (new Model_Votes_Rights())->find($consultation['kid'], $user['uid'])->current();
        $voteGroup = (new Model_Votes_Groups())->find($user['uid'], $subUid, $consultation['kid'])->current();

        $mailService = new Service_Email();

        $confirmationUrl = Zend_Registry::get('baseUrl') . '/voting/confirmvoting/kid/'
            . $consultation['kid'] .'/authcode/' . $votingRights['vt_code'] . '/user/' . $subUid;
        $mail = new Dbjr_Mail();
        $mail
            ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_PARTICIPANTS_REMINDER_VOTER)
            ->setPlaceholders([
                'to_email' => $voteGroup['sub_user'],
                'consultation_title_long' => $consultation['titl'],
                'consultation_title_short' => $consultation['titl_short'],
                'confirmation_url' => $confirmationUrl . '/act/acc/',
                'rejection_url' => $confirmationUrl . '/act/rej/',
            ])
            ->addTo($voteGroup['sub_user']);
        $mailService->queueForSend($mail);

        if ($voteGroup['sub_user'] !== $user['email']) {
            $groupAdminConfirmationUrl = Zend_Registry::get('baseUrl') . '/voting/confirmmember/kid/'
                .  $consultation['kid'] . '/authcode/' . $votingRights['vt_code'] . '/user/' . $subUid;

            $mail = new Dbjr_Mail();
            $mail
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_PARTICIPANTS_REMINDER_GROUP_ADMIN)
                ->setPlaceholders([
                    'to_name' => $user['name'] ? $user['name'] : $user['email'],
                    'to_email' => $user['email'],
                    'voter_email' => $voteGroup['sub_user'],
                    'consultation_title_long' => $consultation['titl'],
                    'consultation_title_short' => $consultation['titl_short'],
                    'confirmation_url' => $groupAdminConfirmationUrl . '/act/'
                        . md5($voteGroup['sub_user'] . $subUid . 'y'),
                    'rejection_url' => $groupAdminConfirmationUrl . '/act/'
                        . md5($voteGroup['sub_user'] . $subUid . 'n'),
                ])
                ->addTo($user['email']);
            $mailService->queueForSend($mail);
        }

        $mailService->sendQueued();
    }
}