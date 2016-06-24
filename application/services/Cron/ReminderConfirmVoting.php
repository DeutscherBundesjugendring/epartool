<?php

class Service_Cron_ReminderConfirmVoting
{
    public function execute()
    {
        $votesIndividual = new Model_Votes_Individual();
        $votesGroups = new Model_Votes_Groups();
        $votingService = new Service_Voting();

        // after half a day after voting
        $unconfirmedVotes = $votesIndividual->getUnconfirmedVotesWithDependencies([
            'v.upd < ?' => (new \DateTime())->modify('-12 hours')->format('Y-m-d H:i:s'),
            'vg.reminders_sent < ?' => 1,
        ]);

        foreach ($unconfirmedVotes as $vote) {
            echo $vote['sub_user'];
            $votingService->sendVoterConfirmationEmail($vote->toArray(), $vote->toArray());
            $votesGroups->update(
                ['reminders_sent' => (int) $vote['reminders_sent'] + 1],
                ['uid = ?' => $vote['uid'], 'sub_uid = ?' => $vote['sub_uid'], 'kid = ?' => $vote['kid']]
            );
        }

        //@TODO weekly reminders

        // a day before voting closes
        $unconfirmedVotes = $votesIndividual->getUnconfirmedVotesWithDependencies([
            'c.vot_to < ?' => (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
            'vg.reminders_sent < ?' => 2,
        ]);

        foreach ($unconfirmedVotes as $vote) {
            $votingService->sendVoterConfirmationEmail($vote->toArray(), $vote->toArray());
            $votesGroups->update(
                ['reminders_sent' => (int) $vote['reminders_sent'] + 1],
                ['uid = ?' => $vote['uid'], 'sub_uid = ?' => $vote['sub_uid'], 'kid = ?' => $vote['kid']]
            );
        }
    }
}
