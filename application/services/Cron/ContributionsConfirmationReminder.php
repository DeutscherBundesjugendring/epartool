<?php

class Service_Cron_ContributionsConfirmationReminder extends Service_Cron
{

    public function execute()
    {
        $contributionModel = new Model_Inputs();
        // after 6 hours
        $unconfirmedContributions = $contributionModel->getUnconfirmedContributions(
            [
                '`when` < ?' => (new \DateTime())->modify('-6 hours')->format('Y-m-d H:i:s'),
                'inp_to >= ?' => (new DateTime())->format('Y-m-d H:i:s'),
            ],
            0
        );

        $userModel = new Model_Users();
        foreach ($unconfirmedContributions as $contribution) {
            $contribution['email'];
            $userModel->sendInputsConfirmationMail(
                $contribution['uid'],
                $contribution['kid'],
                $contribution['confirmation_key'],
                false
            );
            $contributionModel->update(
                ['reminders_sent' => (int) $contribution['reminders_sent'] + 1],
                ['tid' => $contribution['tid']]
            );
        }

        // the last day of contribution phase
        $unconfirmedContributions = $contributionModel->getUnconfirmedContributions(
            [
                'inp_to < ?' => (new DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
                'inp_to >= ?' => (new DateTime())->format('Y-m-d H:i:s'),
            ],
            1
        );

        $userModel = new Model_Users();
        foreach ($unconfirmedContributions as $contribution) {
            $contribution['email'];
            $userModel->sendInputsConfirmationMail(
                $contribution['uid'],
                $contribution['kid'],
                $contribution['confirmation_key'],
                false
            );
            $contributionModel->update(
                ['reminders_sent' => (int) $contribution['reminders_sent'] + 1],
                ['tid' => $contribution['tid']]
            );
        }
    }
}
