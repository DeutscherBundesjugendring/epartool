<?php

class Service_PropertyAjaxUpdate
{
    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param int $uid
     * @param string $subUid
     * @param int $consultationId
     * @throws \Exception
     * @return array
     */
    public function toggleParticipantIsMember(int $uid, string $subUid, int $consultationId)
    {
        return $this->toggleFlag(
            new Model_Votes_Groups(),
            'is_member',
            ['uid = ?' => $uid, 'sub_uid = ?' => $subUid, 'kid = ?' => $consultationId],
            ['1' => null, '0'=> '1', null => '0'],
            [
                '0' => ['label' => 'Denied', 'iconClass' => 'remove', 'labelClass' => 'danger'],
                '1' => ['label' => 'Confirmed', 'iconClass' => 'ok', 'labelClass' => 'success'],
                null => ['label' => 'Unconfirmed', 'iconClass' => 'question-sign', 'labelClass' => 'warning'],
            ]
        );
    }

    /**
     * @param int $contributionId
     * @throws \Exception
     * @return array
     */
    public function toggleContributionIsVotable(int $contributionId)
    {
        return $this->toggleFlag(
            new Model_Inputs(),
            'is_votable',
            ['tid = ?' => $contributionId],
            ['1' => '0', '0' => null, null => '1'],
            [
                '1' => ['label' => 'Yes', 'iconClass' => 'ok', 'labelClass' => 'success'],
                '0' => ['label' => 'No', 'iconClass' => 'remove', 'labelClass' => 'danger'],
                null => ['label' => 'Unknown', 'iconClass' => 'question-sign', 'labelClass' => 'default'],
            ]
        );
    }

    /**
     * @param int $contributionId
     * @throws \Exception
     * @return array
     */
    public function toggleContributionIsConfirmed(int $contributionId)
    {
        return $this->toggleFlag(
            new Model_Inputs(),
            'is_confirmed',
            ['tid = ?' => $contributionId],
            ['1' => null, '0'=> '1', null => '0'],
            [
                '0' => ['label' => 'Blocked', 'iconClass' => 'remove', 'labelClass' => 'danger'],
                '1' => ['label' => 'Confirmed', 'iconClass' => 'ok', 'labelClass' => 'success'],
                null => ['label' => 'Unknown', 'iconClass' => 'question-sign', 'labelClass' => 'default'],
            ]
        );
    }

    /**
     * @param \Dbjr_Db_Table_Abstract $model
     * @param string $name
     * @param array $where
     * @param array $nextValues
     * @param array $buttons
     * @throws \Exception
     * @throws \Zend_Db_Table_Exception
     * @return array
     */
    private function toggleFlag(
        Dbjr_Db_Table_Abstract $model,
        string $name,
        array $where,
        array $nextValues,
        array $buttons
    ):array {
        $entity = $model->fetchRow($where);
        if (!$model->update([$name => $nextValues[$entity[$name]]], $where)) {
            throw new Service_Exception_GroupsEditingException(
                sprintf('Cannot update flag %s of the entity %s', $name, $model->info(Zend_Db_Table_Abstract::NAME))
            );
        }
        $newButton = $buttons[$nextValues[$entity[$name]]];
        $newButton['label'] = $this->translator->translate($newButton['label']);

        return [
            'button' => $newButton,
            'value' => $nextValues[$entity[$name]],
        ];
    }
}
