<?php

class Dbjr_Controller_Action_Helper_ConsultationGetter extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Checks the kid and returns the values from DB if the consultation exists
     * @param array $params
     * @return array
     * @throws Zend_Controller_Action_Exception
     */
    public function direct(array $params)
    {
        if (!empty($params['kid'])) {
            $consultation = (new Model_Consultations())->getById($params['kid']);
            if ($consultation) {
                return $consultation;
            }
        }

        throw new Zend_Controller_Action_Exception('Consultation ID is invalid.', 404);
    }
}
