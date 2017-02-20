<?php

class ConsultationController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $consModel = new Model_Consultations();

        $consultations = $consModel->fetchAll(
            $consModel
                ->select()
                ->where('is_public = ?', true)
                ->order('ord DESC')
        );

        $this->view->consultations = $consultations;
    }
}
