<?php

class ArticleController extends Zend_Controller_Action
{
    private $_consultation;

    protected $_flashMessenger = null;

    public function init()
    {
        $this->_flashMessenger = $this->getHelper('flashMessenger');
        $kid = $this->getRequest()->getParam('kid');

        if ($kid) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->find($kid)->current();
            if ($consultation) {
                $this->_consultation = $consultation;
                $this->view->consultation = $consultation;
            } else {
                $this->redirect('/');
            }
        }
    }

    public function indexAction()
    {
    }

    /**
     * Show single Article
     */
    public function showAction()
    {
        $ref = $this->getRequest()->getParam('ref');
        $aid = $this->getRequest()->getParam('aid');

        $articleModel = new Model_Articles();

        if ($ref) {
            $article = $articleModel->getByRefName($ref);
        } elseif ($aid) {
            $article = $articleModel->getById($aid);
        } else {
            $article = $articleModel->fetchRow(
                $articleModel
                    ->select()
                    ->where('kid=?', $this->_consultation->kid)
                    ->where('ref_nm=?', Model_ArticleRefNames::ARTICLE_EXPLANATION)
                    ->where('hid=?', 'n')
            );
        }

        if ($article) {
            $article['artcl'] = (new Service_Article($this->view->baseUrl()))
                ->placeholderToBasePath($article['artcl']);
            $article['sidebar'] = (new Service_Article($this->view->baseUrl()))
                ->placeholderToBasePath($article['sidebar']);
            $this->view->article = $article;
        } else {
            $this->_flashMessenger->addMessage('Page not found', 'error');
            $this->redirect('/');
        }
    }
}
