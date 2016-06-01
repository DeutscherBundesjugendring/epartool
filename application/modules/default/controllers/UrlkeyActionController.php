<?php

class UrlkeyActionController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    }

    /**
     * Executes all urlkey actions
     */
    public function executeAction()
    {
        $urlkey = $this->getRequest()->getParam('urlkey');
        $urlKeyActionModel = new Model_UrlkeyAction();
        $db = $urlKeyActionModel->getAdapter();
        $db->beginTransaction();
        try {
            $urlkeyAction = $urlKeyActionModel->fetchRow(
                $urlKeyActionModel
                    ->select()
                    ->where('urlkey=?', $urlkey)
                    ->where('time_visited IS NULL')
                    ->where('time_valid_to > NOW() OR time_valid_to IS NULL')
            );

            if ($urlkeyAction) {
                $handler = new $urlkeyAction->handler_class();
                $handler->execute($this->getRequest(), $urlkeyAction);
                $this->view->assign($handler->getViewData());
                $this->_flashMessenger->addMessage($handler->getMessage()['text'], $handler->getMessage()['type']);
                $db->commit();
                if ($handler->getViewName()) {
                    $this->render($handler->getViewName());
                } elseif ($handler->getRedirectUrl()) {
                    $this->redirect($handler->getRedirectUrl());
                } else {
                    $this->redirect('/');
                }
            } else {
                $this->_flashMessenger->addMessage('There is no available action with such key.', 'error');
                $db->commit();
                $this->redirect('/');
            }
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}
