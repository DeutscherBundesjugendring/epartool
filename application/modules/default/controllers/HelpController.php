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
                ->from($helpTextModel->info(Model_HelpText::NAME), ['body', 'name'])
                ->where('name=?', $helpTextName)
                ->where('project_code = ?', Zend_Registry::get('systemconfig')->project)
        );
        $isAjax = false;

        $dom = new DOMDocument();
        $dom->loadHTML($helpText['body'], LIBXML_HTML_NOIMPLIED + LIBXML_HTML_NODEFDTD);
        foreach ($dom->getElementsByTagName('script') as $item) {
          $item->parentNode->removeChild($item);
        }
        $helpText['body'] = $dom->saveHTML();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->setLayout('frontend-help-modal');
            $isAjax = true;
        }

        $this->view->helpText = $helpText;
        $this->view->isAjax = $isAjax;
    }
}
