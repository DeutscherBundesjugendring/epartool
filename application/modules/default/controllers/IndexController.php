<?php

class IndexController extends Zend_Controller_Action
{
    const LAST_CONSULTATION_COUNT = 3;

    /**
     * The home page
     */
    public function indexAction()
    {
        $consModel = new Model_Consultations();
        $consCount = $consModel->fetchRow(
            $consModel
                ->select()
                ->from($consModel->info(Model_Consultations::NAME), ['count' => 'COUNT(*)'])
                ->where('public = ?', 'y')
        )
        ->count;

        $this->view->consultations = $consModel->getLast(self::LAST_CONSULTATION_COUNT);
        $this->view->showMoreButton = $consCount > self::LAST_CONSULTATION_COUNT;
    }

    /**
     * Perform search and display results
     */
    public function searchAction()
    {
        $needle = $this->getRequest()->getParam('q', 0);

        if ($needle) {
            $filterChain = new Zend_Filter();
            $filterChain->appendFilter(new Zend_Filter_StringTrim());
            $filterChain->appendFilter(new Zend_Filter_StringToLower(array('encoding' => 'UTF-8')));
            $filterChain->appendFilter(new Zend_Filter_HtmlEntities());
            $needle = $filterChain->filter($needle);

            $articles = new Model_Articles();
            $consultation = new Model_Consultations();
            $followUps = new Model_Followups();

            $this->view->needle = $needle;
            $this->view->resultsGeneral = $articles->search($needle);
            $this->view->resultsConsultations = $consultation->search($needle);
            $this->view->resultsFollowUps = $followUps->search($needle);
        } else {
            $this->redirect('');
        }
    }

    /**
     * Echoes a javascript object with translated messages.
     * Headers are set to application/javascript
     */
    public function i18nAction()
    {
        $i18n = [
            'Weak' => $this->view->translate('Weak'),
            'Normal' => $this->view->translate('Normal'),
            'Medium' => $this->view->translate('Medium'),
            'Strong' => $this->view->translate('Strong'),
            'Very Strong' => $this->view->translate('Very Strong'),
        ];

        header('Content-Type: application/javascript; charset=utf-8');
        echo 'var i18n = ' . json_encode($i18n);
        die();
    }
}
