<?php

class Admin_ArticleController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_adminIndexURL = null;

    private $_kid;
    private $_consultation;

    /**
     * @desc Construct
     * @return void
     */
    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
        $this->_adminIndexURL = $this->view->url(array(
            'controller' => 'index',
            'action' => 'index'
        ));
        $kid = $this->_request->getParam('kid', null);
        if ($kid) {
            $this->_kid = $kid;
            $this->view->kid = $kid;
            $this->_consultation = (new Model_Consultations())->getById($kid);
            $this->view->consultation = $this->_consultation;
        }
    }

    /**
     * @desc show Articles Form
     * @return void
     */
    public function indexAction()
    {
        $articles = null;
        $articleModel = new Model_Articles();
        if ($this->_kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($this->_kid);
            $this->view->mainArticlesSum = $articleModel
                ->getCountByConsultationAndType($this->_kid, 'article_explanation');
            if (!empty($consultation)) {
                $this->view->consultation = $consultation;
                $articles = $consultation['articles'];
            } else {
                $this->_redirect($this->_adminIndexURL, array('prependBase' => false));
            }
        } else {
            $articles = $articleModel->getAllWithoutConsultation();
        }

        $this->view->articles = $articles;
        $this->view->form = new Admin_Form_ListControl();
    }

    public function createAction()
    {
        $isPreview = $this->getRequest()->getPost('preview');
        if (!empty($isPreview)) {
            $this->_forward('preview');
        } else {
            $form = new Admin_Form_Article($this->_kid);
            $form->setAction($this->view->baseUrl() . '/admin/article/create/kid/' . $this->_kid);
            $this->populateRefNames($form, $this->_kid);
            $articleModel = new Model_Articles();
            $parentOptions = [0 => $this->view->translate('None')];
            if ($this->_kid) {
                $firstLevelPages = $articleModel->getFirstLevelEntries($this->_kid);
                foreach ($firstLevelPages as $page) {
                    $parentOptions[$page['art_id']] = '[' . $page['art_id'] . '] ' . $page['desc'];
                }
            }
            $form->getElement('parent_id')->setMultiOptions($parentOptions);
            $isRetFromPreview = $this->getRequest()->getPost('backFromPreview');
            if ($this->getRequest()->isPost() && empty($isRetFromPreview)) {
                $article = $this->getRequest()->getPost();
                $article = $this->setProject($article);
                if ($form->isValid($article)) {
                    $values = $form->getValues();
                    $articleModel = new Model_Articles();
                    $articleRow = $articleModel->createRow();
                    $this->updateArticleRow($articleRow, $values);
                    $articleRow->kid = $this->_kid;
                    $newId = $articleRow->save();
                    if ($newId > 0) {
                        $this->_flashMessenger->addMessage('New article has been created.', 'success');
                    } else {
                        $this->_flashMessenger->addMessage('Creating new article failed.', 'error');
                    }

                    $this->redirect(
                        $this->view->url(['action' => 'index', 'kid' => $this->_kid]),
                        ['prependBase' => false]
                    );
                } else {
                    $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                    $form->populate($form->getValues());
                    $form->getElement('proj')->setValue($article['proj']);
                }
            } elseif ($this->getRequest()->isPost() && !empty($isRetFromPreview)) {
                $article = $this->getRequest()->getPost();
                $articlePreviewForm = new Admin_Form_ArticlePreview();
                if ($articlePreviewForm->isValid($article)) {
                    $article['proj'] = unserialize($article['proj']);
                    $form->populate($article);
                } else {
                    $this->_redirect('admin');
                }
            }

            $this->view->form = $form;
        }
    }

    public function editAction()
    {
        $isPreview = $this->getRequest()->getPost('preview');
        if (!empty($isPreview)) {
            $this->_forward('preview');
        } else {
            $aid = $this->getRequest()->getParam('aid', 0);
            if ($aid > 0) {
                $articleModel = new Model_Articles();
                $form = new Admin_Form_Article($this->_kid);
                $this->populateRefNames($form, $this->_kid);
                $parentOptions = [0 => $this->view->translate('None')];
                if ($this->_kid) {
                    $firstLevelPages = $articleModel->getFirstLevelEntries($this->_kid);
                    foreach ($firstLevelPages as $page) {
                        if ($page['art_id'] != $aid) {
                            $parentOptions[$page['art_id']] = '[' . $page['art_id'] . '] ' . $page['desc'];
                        }
                    }
                }
                $form->getElement('parent_id')->setMultiOptions($parentOptions);
                $isRetFromPreview = $this->getRequest()->getPost('backFromPreview');
                $article = $articleModel->getById($aid);

                if (!$article) {
                    $this->_flashMessenger->addMessage('This article does not exist.', 'error');
                    $this->redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
                }

                $mainArticleSum = $articleModel->getCountByConsultationAndType($this->_kid, 'article_explanation');
                if ($article['ref_nm'] === 'article_explanation') {
                    if ($mainArticleSum === 1 && $article['is_showed']) {
                        $form->getElement('is_showed')->setAttrib('disabled', 'disabled')
                            ->setAttrib('disableHidden', true);
                    }
                }
                if ($this->getRequest()->isPost() && empty($isRetFromPreview)) {
                    $article = $this->getRequest()->getPost();
                    $article = $this->setProject($article);
                    if ($form->isValid($article)) {
                        $values = $form->getValues();
                        if (!isset($article['is_showed'])) {
                            unset($values['is_showed']);
                        }
                        $articleRow = $articleModel->find($aid)->current();

                        if ($articleRow['ref_nm'] === 'article_explanation') {
                            if ($mainArticleSum === 1 && $articleRow['is_showed'] && isset($article['is_showed'])
                                && !$values['is_showed']
                            ) {
                                $this->_flashMessenger->addMessage('This article could not be hidden.', 'error');
                                $this->redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
                            }
                        }

                        $this->updateArticleRow($articleRow, $values);
                        $articleRow->save();
                        $article = $articleRow->toArray();
                        $article['proj'] = explode(',', $articleRow->proj);
                        $this->_flashMessenger->addMessage('Changes saved.', 'success');
                    } else {
                        $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                    }
                } elseif ($this->getRequest()->isPost() && !empty($isRetFromPreview)) {
                    $article = $this->getRequest()->getPost();
                    $articlePreviewForm = new Admin_Form_ArticlePreview();
                    if ($articlePreviewForm->isValid($article)) {
                        $article['proj'] = unserialize($article['proj']);
                    } else {
                        $this->redirect('admin');
                    }
                } else {
                    $article['proj'] = explode(',', $article['proj']);
                }

                $form->populate($article);
            }

            $this->view->form = $form;
        }
    }

    /**
     * Deletes an article
     */
    public function deleteAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $articleModel = new Model_Articles();
            $id = $this->getRequest()->getPost('delete');
            $article = $articleModel->find($id)->current()->toArray();
            if ($article['ref_nm'] === 'article_explanation') {
                if ($articleModel->getCountByConsultationAndType($article['kid'], 'article_explanation') === 1
                    && $article['is_showed']) {
                    $this->_flashMessenger->addMessage('This article could not be deleted.', 'error');
                    $this->_redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
                }
            }

            $nrDeleted = $articleModel->deleteById($id);
            if ($nrDeleted) {
                $this->_flashMessenger->addMessage('Article has been deleted.', 'success');
            } else {
                $this->_flashMessenger->addMessage('Article could not be deleted. If there are any sub-articles, these have to be removed first.', 'error');
            }
        }

        $this->_redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
    }

    /**
     * Shows a preview of an article as it would apper on the frontend.
     * This method is allways only being used as a target of a _forward() method.
     */
    protected function previewAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if (isset($data['preview'])) {
                $this->getResponse()->setHeader('X-XSS-Protection', 0);
                $articlePreviewForm = new Admin_Form_ArticlePreview();
                $data['proj'] = serialize(isset($data['proj']) ? $data['proj'] : []);
                $articlePreviewForm->populate($data);

                $prevView = new Zend_View();
                $prevView->addScriptPath(APPLICATION_PATH . '/modules/default/views/scripts');
                $prevView->addHelperPath(
                    APPLICATION_PATH . '/modules/default/views/helpers',
                    'Module_Default_View_Helper'
                );
                $prevView->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Application_View_Helper');
                $this->_helper->layout->setLayout('frontend');
                $this->_helper->layout->setView($prevView);

                $this->view->prevView = $prevView;
                $this->view->articlePreviewForm = $articlePreviewForm;
                $this->view->article = array_merge((new Model_Articles())->getById($data['art_id']), $data);
                $this->render('preview');
            } elseif (isset($data['backFromPreview'])) {
                if ($data['isNew'] == 1) {
                    $this->forward('create');
                } else {
                    $this->forward('edit');
                }
            }
        }
    }

    /**
     * Sats the project in case it is not set or the current project is not set
     * @param  array $data The data to be adjusted
     * @return array The adjsuted data
     */
    protected function setProject($data)
    {
        if (!isset($data['proj']) || empty($data['proj'])) {
            // project should not be empty
            $data['proj'] = array(Zend_Registry::get('systemconfig')->project);
        }
        if (!in_array(Zend_Registry::get('systemconfig')->project, $data['proj'])) {
            // current project always has to be set!
            $data['proj'][] = Zend_Registry::get('systemconfig')->project;
        }

        return $data;
    }

    /**
     * Returns multiOptions for field ref_nm in Admin_Form_Article by type
     * @return array
     */
    private function getMultioptionsByType($type = null)
    {
        $options = array();
        if (is_null($type)) {
            return $options;
        }

        $rowSet = (new Model_ArticleRefNames())->getAllByType($type);
        foreach ($rowSet as $row) {
            $options[$row->ref_nm] = $row->desc
                . ' [' . Zend_Registry::get('Zend_Translate')->translate('Area:') . ' ' . $row->scope . ']';
        }

        return $options;
    }

    /**
     * @param /Zend_Db_Table_Row_Abstract $articleRow
     * @param array $values
     */
    private function updateArticleRow(Zend_Db_Table_Row_Abstract $articleRow, $values)
    {
        $articleRow->setFromArray($values);
        $articleRow->ref_nm = $articleRow->ref_nm ? $articleRow->ref_nm : null;
        $articleRow->proj = implode(',', $values['proj']);
        $articleRow->parent_id = $articleRow->parent_id ? $articleRow->parent_id : null;
        $articleRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
    }

    /**
     * @param \Zend_Form $form
     * @param int $kid
     */
    private function populateRefNames(Zend_Form $form, $kid)
    {
        $multiOptions = [0 => $this->view->translate('Please select…')];
        if ($kid > 0) {
            foreach ($this->getMultioptionsByType(Model_ArticleRefNames::TYPE_CONSULTATION) as $key => $value) {
                $multiOptions[$key] = $value;
            }
            $form->getElement('ref_nm')->setMultioptions($multiOptions);
            $form->getElement('ref_nm')->setDescription('On subpages, reference name of parent page is used.');
        } else {
            foreach ($this->getMultioptionsByType(Model_ArticleRefNames::TYPE_GLOBAL) as $key => $value) {
                $multiOptions[$key] = $value;
            }
            $form->getElement('ref_nm')->setMultioptions($multiOptions);
        }
    }
}
