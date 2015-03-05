<?php

class HelpController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $helpTextModel = new Model_HelpText();
        $helpTextName = $this->getRequest()->getParam('name');
        $helpText = $helpTextModel->fetchRow(
            $helpTextModel
                ->select()
                ->where('name=?', $helpTextName)
                ->from($helpTextModel->info(Model_HelpText::NAME), ['body', 'name'])
        );

        $this->view->helpText = $helpText;
    }
}
