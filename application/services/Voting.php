<?php

class Service_Voting
{

    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_VOTED = 'voted';
    const STATUS_SKIPPED = 'skipped';

    const BUTTONS_TYPE_STARS = 'stars';
    const BUTTONS_TYPE_HEARTS = 'hearts';
    const BUTTONS_TYPE_YESNO = 'yesno';

    const BUTTON_SKIP = 'Skip';

    const BUTTON_UNDECIDED = 'No Opinion';

    const BUTTONS_SET = [
        self::BUTTONS_TYPE_STARS => [
            0 => 'Strongly disagree',
            1 => 'Disagree',
            2 => 'Somewhat disagree',
            3 => 'Somewhat agree',
            4 => 'Agree',
            5 => 'Strongly agree',
        ],
        self::BUTTONS_TYPE_HEARTS => [
            0 => 'Strongly disagree',
            1 => 'Disagree',
            2 => 'Somewhat disagree',
            3 => 'Somewhat agree',
            4 => 'Agree',
            5 => 'Strongly agree',
        ],
        self::BUTTONS_TYPE_YESNO => [
            -1 => 'Disagree',
            0 => 'Unsure',
            1 => 'Agree',
        ],
    ];

    /**
     * @param int $consultationId
     * @param int $points
     * @return bool
     */
    public function isPointsInValidRange($consultationId, $points)
    {
        if ($consultationId < 1) {
            return false;
        }
        if ($points === null) {
            return true;
        }
        $votingSettings = (new Model_Votes_Settings())->getById($consultationId);

        return (array_key_exists($points, self::BUTTONS_SET[$votingSettings['button_type']]));
    }

    /**
     * @return string
     */
    public function generateConfirmationHash()
    {
        return md5(uniqid('confirm_vote', true));
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
        if (empty($oneVoteToHandle)) {
            throw new Dbjr_Voting_NoVotesException();
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

        if ($votingGroup['is_member'] === null) {
            $this->sendUserConfirmationEmail($oneVoteToHandle, $votingGroup);
        }
    }

    /**
     * @param string $hash
     * @return int
     * @throws Dbjr_Voting_Exception
     */
    public function rejectVotes($hash)
    {
        $modelVotesIndividual = new Model_Votes_Individual();

        $voteWithDependencies = $modelVotesIndividual->getOneVoteWithDependencies($hash);

        if (empty($voteWithDependencies)) {
            throw new Dbjr_Voting_NoVotesException();
        }

        $result = $modelVotesIndividual->delete([
            'confirmation_hash = ?' => $hash,
            'status = ?' => self::STATUS_VOTED,
        ]);

        if (empty($modelVotesIndividual->fetchAll(['sub_uid = ?' => $voteWithDependencies['sub_uid']])->current())) {
            (new Model_Votes_Groups())->delete([
                'kid = ?' => $voteWithDependencies['kid'],
                'uid = ?' => $voteWithDependencies['uid'],
                'sub_uid = ?' => $voteWithDependencies['sub_uid'],
            ]);
        }

        return $result;
    }

    /**
     * @param array $vote
     * @param string $confirmationHash
     * @throws \Dbjr_Voting_Exception
     * @throws \Zend_Db_Statement_Exception
     */
    public function saveVote($vote, $confirmationHash)
    {
        $votesModel = new Model_Votes_Individual();

        if (!$this->isVoteValid($vote)) {
            throw new Dbjr_Voting_Exception('Vote is invalid');
        }

        if(!$votesModel->updateVote($vote['tid'], $vote['sub_uid'], $vote['uid'], $vote['pts'], $confirmationHash)) {
            throw new Dbjr_Voting_Exception('Cannot save vote');
        };
    }

    /**
     * @param \Zend_Auth $auth
     * @param $confirmationHash
     * @throws \Dbjr_Voting_MissingGroupLeaderException
     * @throws \Dbjr_Voting_MissingVotingGroupException
     * @throws \Dbjr_Voting_MissingVotingRightsException
     * @throws \Dbjr_Voting_NoVotesException
     * @throws \Zend_Db_Table_Exception
     * @return bool
     */
    public function stopVoting(Zend_Auth $auth, $confirmationHash)
    {
        $modelVotesIndividual = new Model_Votes_Individual();
        $modelVotesGroups = new Model_Votes_Groups();

        $voteWithDependencies = $modelVotesIndividual->getOneVoteWithDependencies($confirmationHash);

        if (empty($voteWithDependencies)) {
            throw new Dbjr_Voting_NoVotesException();
        }

        $groupLeader = (new Model_Users())->find($voteWithDependencies['uid'])->current();
        if (!$groupLeader) {
            throw new Dbjr_Voting_MissingGroupLeaderException();
        }
        $group = $modelVotesGroups->find(
            $voteWithDependencies['uid'],
            $voteWithDependencies['sub_uid'],
            $voteWithDependencies['kid']
        )->current();
        if (!$group) {
            throw new Dbjr_Voting_MissingVotingGroupException();
        }

        $userConfirmationEmailSent = false;
        if ($group['sub_user'] === $groupLeader['email']) {
            if ($auth->hasIdentity() && $auth->getIdentity()->email === $group['sub_user']) {

                $modelVotesIndividual->setStatusForSubuser(
                    $confirmationHash,
                    Service_Voting::STATUS_CONFIRMED,
                    Service_Voting::STATUS_VOTED
                );
                $modelVotesGroups->update(['is_member' => true], [
                    'uid = ?' => $voteWithDependencies['uid'],
                    'sub_uid = ?' => $voteWithDependencies['sub_uid'],
                    'kid = ?' => $voteWithDependencies['kid'],
                ]);
            } else {
                $this->sendVoterConfirmationEmail($voteWithDependencies, $group->toArray());
                $userConfirmationEmailSent = true;
            }
        } else {
            if ($auth->getIdentity() && $group['sub_user'] === $auth->getIdentity()->email) {

                $modelVotesIndividual->setStatusForSubuser(
                    $confirmationHash,
                    Service_Voting::STATUS_CONFIRMED,
                    Service_Voting::STATUS_VOTED
                );
                if ($group['is_member'] === null) {
                    $this->sendUserConfirmationEmail($voteWithDependencies, $group->toArray());
                }
            } else {
                $this->sendVoterConfirmationEmail($voteWithDependencies, $group->toArray());
                $userConfirmationEmailSent = true;
            }
        }

        return $userConfirmationEmailSent;
    }

    /**
     * @param array $vote
     * @return bool
     */
    private function isVoteValid(array $vote)
    {
        if (empty($vote['tid']) || empty($vote['sub_uid']) || empty($vote['uid'])
            || !(new Service_Voting())->isPointsInValidRange(
                (new Model_Inputs())->getConsultationIdByContribution($vote['tid']),
                $vote['pts']
            )
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
    public function sendUserConfirmationEmail(array $vote, array $votingGroup)
    {
        $votingRight = (new Model_Votes_Rights())->find($vote['kid'], $vote['uid'])->current();
        if ($votingRight === null) {
            throw new Dbjr_Voting_MissingVotingRightsException();
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

    /**
     * @param array $vote
     * @param array $votingGroup
     * @throws \Dbjr_Mail_Exception
     * @throws \Zend_Exception
     */
    public function sendVoterConfirmationEmail(array $vote, array $votingGroup)
    {

        $actionUrl = Zend_Registry::get('baseUrl') . '/voting/confirmvoting/kid/' . $vote['kid'] .
            '/hash/' . $vote['confirmation_hash'];

        $mailer = new Dbjr_Mail();
        $mailer
            ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_CONFIRMATION_SINGLE)
            ->setPlaceholders(
                array(
                    'to_email' => $votingGroup['sub_user'],
                    'confirmation_url' => $actionUrl,
                    'consultation_title_short' => $vote['titl_short'],
                    'consultation_title_long' => $vote['titl'],
                )
            )
            ->addTo($votingGroup['sub_user']);
        (new Service_Email)
            ->queueForSend($mailer)
            ->sendQueued();
    }
}
