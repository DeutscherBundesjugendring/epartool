<?php

class Service_Groups
{

    const SQL_STATE_CODE_CANNOT_DELETE = 23000;

    /**
     * @param Zend_Db_Table_Row $consultation
     * @param int $from
     * @param int $to
     * @return int
     */
    public function guessGroupAge($consultation, $from, $to)
    {
        if ($from === null) {
            return -1;
        }

        $group = (new Model_ContributorAge())->fetchRow([
            '`consultation_id` = ?' => $consultation['kid'],
            '`from` = ?' => (int) $from,
            '`to` = ?' => (int) $to,
        ]);
        if ($group) {
            return $group['id'];
        }

        return -1;
    }

    /**
     * @param array $groupAges
     * @param int $infinityFrom
     * @param \Zend_Db_Table_Row_Abstract $consultation
     */
    public function updateGroupAges(array $groupAges, $infinityFrom, Zend_Db_Table_Row_Abstract $consultation)
    {
        $this->update(new Model_ContributorAge(), $groupAges, $infinityFrom, $consultation);
    }

    /**
     * @param array $groupSizes
     * @param int $infinityFrom
     * @param \Zend_Db_Table_Row_Abstract $consultation
     */
    public function updateGroupSizes(array $groupSizes, $infinityFrom, Zend_Db_Table_Row_Abstract $consultation)
    {
        $this->update(new Model_GroupSize(), $groupSizes, $infinityFrom, $consultation);
    }

    /**
     * @param \Dbjr_Db_Table_Abstract $model
     * @param array $elements
     * @param int $infinityFrom
     * @param \Zend_Db_Table_Row_Abstract $consultation
     * @throws \Service_Exception_GroupsException
     * @throws \Zend_Db_Statement_Exception
     */
    private function update(
        Dbjr_Db_Table_Abstract $model,
        array $elements,
        $infinityFrom,
        Zend_Db_Table_Row_Abstract $consultation
    ) {
        $existingElements = $model->getByConsultation($consultation['kid']);

        $original = [];
        $edit = [];
        $delete = [];

        foreach ($existingElements as $existingElement) {
            if ($existingElement['to'] === null
                || ((int) $existingElement['from'] === 1 && (int) $existingElement['to'] === 1)) {
                continue;
            }
            if (isset($elements[$existingElement['id']])) {
                if ((int) $elements[$existingElement['id']]['from'] !== (int) $existingElement['from']
                    || (int) $elements[$existingElement['id']]['to'] !== (int) $existingElement['to']) {
                    $edit[$existingElement['id']] = $elements[$existingElement['id']];
                }
                $original[$existingElement['id']] = $existingElement;
                unset($elements[$existingElement['id']]);
            } else {
                if ($model instanceof Model_ContributorAge
                    || ((int) $existingElement['from'] !== 1 && (int) $existingElement['to'] !== 2)) {
                    $delete[$existingElement['id']] = $existingElement->toArray();
                }
            }
        }

        $new = $elements;

        foreach ($delete as $id => $del) {
            try {
                $model->delete(['id = ?' => $id,'consultation_id = ?' => $consultation['kid']]);
            } catch (Zend_Db_Statement_Exception $e) {
                if ($e->getCode() === self::SQL_STATE_CODE_CANNOT_DELETE) {
                    if ($model instanceof Model_ContributorAge) {
                        throw (new Service_Exception_GroupsDeletingException())->setInterval($del)
                            ->setIntervalGroup('contributorAges');
                    } else {
                        throw (new Service_Exception_GroupsDeletingException())->setInterval($del)
                            ->setIntervalGroup('groupSizes');
                    }
                } else {
                    throw $e;
                }
            }
        }

        if ($model instanceof Model_ContributorAge) {
            $options = array_keys($model->getOptionsByConsultation($consultation['kid']));
            if (empty($options)) {
                $canEdit = true;
            } else {
                $canEdit = (new Model_User_Info())->fetchAll(['age_group IN (?)' => $options])->count() === 0;
            }
        } else {
            $options = array_keys($model->getOptionsByConsultation($consultation['kid']));
            if (empty($options)) {
                $canEdit = true;
            } else {
                $canEdit = (new Model_Votes_Rights())->fetchAll(['grp_siz IN (?)' => $options])->count() === 0;
            }
        }

        if (count($edit) > 0) {
            if (!$canEdit) {
                if ($model instanceof Model_ContributorAge) {
                    throw (new Service_Exception_GroupsEditingException())->setInterval($original)
                        ->setIntervalGroup('contributorAges');
                } else {
                    $ids = array_keys($model->getOptionsByConsultation($consultation['kid']));
                    if ((new Model_Votes_Rights())->fetchAll(['grp_siz IN (?)' => $ids])->count() > 0) {
                        throw (new Service_Exception_GroupsEditingException())->setInterval($original)
                            ->setIntervalGroup('groupSizes');
                    }
                }
            }

            foreach ($edit as $id => $ed) {
                if (!$ed['from'] || !$ed['to']) {
                    continue;
                }
                $model->update(
                    ['from' => $ed['from'], 'to' => $ed['to']],
                    ['id = ?' => $id, 'consultation_id = ?' => $consultation['kid']]
                );
            }
        }

        foreach ($new as $n) {
            if (!$n['from'] || !$n['to']) {
                continue;
            }

            $model->insert(
                ['from' => $n['from'], 'to' => $n['to'], 'consultation_id' => $consultation['kid']]
            );
        }

        if ($model instanceof Model_ContributorAge) {
            $infinityCriteria = ['consultation_id = ?' => $consultation['kid'], '`to` IS NULL', '`from` != ?' => 1];
        } else {
            $infinityCriteria = ['consultation_id = ?' => $consultation['kid'], '`to` IS NULL'];
        }
        if ($infinityFrom > 0) {
            $infinityItem = $model->fetchRow($infinityCriteria);
            if ($infinityItem === null) {
                $model->insert(['consultation_id' => $consultation['kid'], 'from' => (int) $infinityFrom]);
            } elseif ($infinityItem['from'] !== $infinityFrom) {
                if (!$canEdit) {
                    if ($model instanceof Model_ContributorAge) {
                        throw (new Service_Exception_GroupsEditingException())->setToInfinityOriginal([
                            'toInfinityAgeFrom' => $infinityItem['from'],
                        ]);
                    } else {
                        throw (new Service_Exception_GroupsEditingException())->setToInfinityOriginal([
                            'toInfinitySizeFrom' => $infinityItem['from'],
                        ]);
                    }

                }
                $model->update(['from' => (int) $infinityFrom], $infinityCriteria);
            }
        } else {
            try {
                $model->delete($infinityCriteria);
            } catch (Zend_Db_Statement_Exception $e) {
                if ($e->getCode() === self::SQL_STATE_CODE_CANNOT_DELETE) {
                    throw new Service_Exception_GroupsDeletingException();
                } else {
                    throw $e;
                }
            }
        }
    }
}
