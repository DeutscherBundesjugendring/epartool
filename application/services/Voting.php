<?php

class Service_Voting
{

    const STATUS_CONFIRMED = 'c';
    const STATUS_VOTED = 'v';
    const STATUS_SKIPPED = 's';

    /**
     * @return string
     */
    public function generateConfirmationHash()
    {
        return md5(uniqid('confirm_vote', true));
    }

    /**
     * @param int $kid
     */
    public function getVotingResults($kid)
    {
        $votesModel = new Model_Votes_Individual();
    }

    /**
     * @param string $confirmationHash
     * @throws Dbjr_Voting_Exception
     * @return int
     */
    public function confirmVotes($confirmationHash)
    {
        $votingIndividualModel = new Model_Votes_Individual();
        $oneVoteToHandle = $votingIndividualModel->getOneVoteWithDependencies($confirmationHash);
        if ($oneVoteToHandle === null) {
            throw new Dbjr_Voting_Exception('No votes to handle');
        }

        $result = $votingIndividualModel->update(
            ['upd' => new Zend_Db_Expr('NOW()'), 'confirmation_hash' => null, 'status' => self::STATUS_CONFIRMED],
            ['confirmation_hash = ?' => $confirmationHash]
        );
        
        if (!$result) {
            throw new Dbjr_Voting_Exception('No votes to confirm.');
        }
        
        $votingGroupModel = new Model_Votes_Groups();
        $votingGroup = $votingGroupModel->getByUser(
            $oneVoteToHandle['uid'],
            $oneVoteToHandle['sub_uid'],
            $oneVoteToHandle['kid']
        );

        if (empty($votingGroup)) {
            throw new Dbjr_Voting_Exception('Voting group not found.');
        }

        if ($votingGroup['member'] === 'u') {
            $this->sendUserConfirmationEmail($oneVoteToHandle, $votingGroup);
        }
    }

    /**
     * @param string $hash
     * @return int
     */
    public function rejectVotes($hash)
    {
        return (new Model_Votes_Individual())->delete([
            'confirmation_hash = ?' => $hash,
            'status = ?' => self::STATUS_VOTED,
        ]);
    }

    /**
     * @param array $vote
     * @param string $confirmationHash
     * @throws \Dbjr_Voting_Exception
     */
    public function saveVote($vote, $confirmationHash)
    {
        $votesModel = new Model_Votes_Individual();
        
        if (!$this->isVoteValid($vote)) {
            throw new Dbjr_Voting_Exception('Vote is invalid');
        }
        
        $vote['confirmation_hash'] = $confirmationHash;
        $vote['upd'] = new Zend_Db_Expr('NOW()');
        $vote['status'] = 'v';
        
        if (empty($votesModel->createRow($vote)->save())) {
            throw new Dbjr_Voting_Exception('Cannot save vote');
        }
    }

    /**
     * @param array $vote
     * @return bool
     */
    private function isVoteValid(array $vote)
    {
        if (empty($vote['tid']) || empty($vote['sub_uid']) || empty($vote['uid'])
            || $vote['pts'] < 0 || $vote['pts'] > 5
        ) {
            return false;
        }
        
        return true;
    }

    /**
     * @param array $vote
     * @param array $votingGroup
     * @throws \Dbjr_Mail_Exception
     * @throws \Dbjr_Voting_Exception
     * @throws \Zend_Exception
     */
    private function sendUserConfirmationEmail($vote, $votingGroup)
    {
        $votingRight = (new Model_Votes_Rights())->fetchRow(['kid' => $vote['kid'], 'uid' => $vote['uid']]);
        if ($votingRight === null) {
            throw new Dbjr_Voting_Exception('No voting rights found.');
        }

        // get groupleader
        $userModel = new Model_Users();
        $leader = $userModel->getById($votingGroup['uid']);
        $actionUrl = Zend_Registry::get('baseUrl') . '/voting/confirmmember/kid/' .  $vote['kid']
            . '/authcode/' . $votingRight['vt_code'] . '/user/' . $votingGroup['sub_uid'];

        $mailer = new Dbjr_Mail();
        $mailer
            ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_CONFIRMATION_GROUP)
            ->setPlaceholders(
                array(
                    'to_name' => $leader['name'] ? $leader['name'] : $leader['email'],
                    'to_email' => $leader['email'],
                    'voter_email' => $votingGroup['sub_user'],
                    'confirmation_url' => $actionUrl . '/act/'
                        . md5($votingGroup['sub_user'] . $votingGroup['sub_uid'] . 'y'),
                    'rejection_url' => $actionUrl . '/act/'
                        . md5($votingGroup['sub_user'] . $votingGroup['sub_uid'] . 'n'),
                    'consultation_title_short' => $vote['titl_short'],
                    'consultation_title_long' => $vote['titl'],
                )
            )
            ->addTo($leader['email']);
        (new Service_Email)
            ->queueForSend($mailer)
            ->sendQueued();
    }
}
