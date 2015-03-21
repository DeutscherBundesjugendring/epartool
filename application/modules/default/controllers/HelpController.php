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

        $dom = new DOMDocument();
        $dom->loadHTML($helpText['body'], LIBXML_HTML_NOIMPLIED + LIBXML_HTML_NODEFDTD);
        foreach ($dom->getElementsByTagName('script') as $item) {
          $item->parentNode->removeChild($item);
        }
        $helpText['body'] = $dom->saveHTML();

        $this->view->helpText = $helpText;
    }
}
