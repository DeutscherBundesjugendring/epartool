<?php
/**
 * Plugin for Navigation Component, currently used in admin module only
 * @author Markus
 *
 */
class Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
    /**
     * Set active pages
     * @see Zend_Controller_Plugin_Abstract::postDispatch()
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $view = Zend_Layout::getMvcInstance()->getView();

        $kid = $request->getParam('kid', 0);
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->find($kid)->current();
            $page = $view->navigation()->findOneByLabel('Beteiligungsrunden');
            if ($page) {
                $page->setActive();
            }
            if ($consultation) {
                $subpage = $view->navigation()->findOneByLabel($consultation->titl);
                if ($subpage) {
                    $subpage->setActive();
                }
            }
        }

        // JSU set user to active page if uid exists
        if ($this->getRequest()->controller == 'user') {
            $pageUser = $view->navigation()->findOneByLabel('Nutzerverwaltung');
            if ($pageUser) {
                $pageUser->setActive();
            }
        }

        // JSU set user to active page if uid exists
        if ($this->getRequest()->controller == 'email' && $this->getRequest()->action=='edittemplate') {
            $pageUser = $view->navigation()->findOneByLabel('E-Mail-Vorlagen');
            if ($pageUser) {
                $pageUser->setActive();
            }
        }

    }
}
