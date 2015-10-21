<?php

class Admin_ArticleController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    protected $_adminIndexURL = null;

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
        $kid = $this->getRequest()->getParam('kid', 0);
        $articles = null;
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($kid);
            if (!empty($consultation)) {
                $this->view->consultation = $consultation;
                $articles = $consultation['articles'];
            } else {
                $this->_redirect($this->_adminIndexURL, array('prependBase' => false));
            }
        } else {
            $articleModel = new Model_Articles();
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
            $kid = $this->getRequest()->getParam('kid', 0);
            $consultation = null;
            $form = null;
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($kid);
            $form = new Admin_Form_Article($kid);
            $form->setAction($this->view->baseUrl() . '/admin/article/create/kid/' . $kid);
            $multiOptions = array(0 => $this->view->translate('Please select…'));
            if ($kid > 0) {
                // set multiOptions for ref_nm
                foreach ($this->getMultioptionsByType('b') as $key => $value) {
                    $multiOptions[$key] = $value;
                }
                $form->getElement('ref_nm')->setMultioptions($multiOptions);
                $form->getElement('ref_nm')->setDescription('On subpages, reference name of parent page is used.');
            } else {
                // set multiOptions for ref_nm
                foreach ($this->getMultioptionsByType('g') as $key => $value) {
                    $multiOptions[$key] = $value;
                }
                $form->getElement('ref_nm')->setMultioptions($multiOptions);
            }
            $articleModel = new Model_Articles();
            $firstLevelPages = $articleModel->getFirstLevelEntries($kid);
            $parentOptions = array(
                0 => $this->view->translate('None')
            );
            foreach ($firstLevelPages as $page) {
                $parentOptions[$page['art_id']] = '[' . $page['art_id'] . '] ' . $page['desc'];
            }
            $form->getElement('parent_id')->setMultiOptions($parentOptions);
            $isRetFromPreview = $this->getRequest()->getPost('backFromPreview');
            if ($this->getRequest()->isPost() && empty($isRetFromPreview)) {
                $data = $this->getRequest()->getPost();
                $data = $this->setProject($data);
                if ($form->isValid($data)) {
                    $articleModel = new Model_Articles();
                    $articleRow = $articleModel->createRow($form->getValues());
                    $articleRow->kid = $kid;
                    $articleRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
                    $articleRow->proj = implode(',', $data['proj']);
                    $newId = $articleRow->save();
                    if ($newId > 0) {
                        $this->_flashMessenger->addMessage('New article has been created.', 'success');
                    } else {
                        $this->_flashMessenger->addMessage('Creating new article failed.', 'error');
                    }

                    $this->_redirect($this->view->url(array(
                        'action' => 'index',
                        'kid' => $kid
                    )), array('prependBase' => false));
                } else {
                    $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                    $form->populate($form->getValues());
                    $form->getElement('proj')->setValue($data['proj']);
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

            foreach ($form->getElements() as $element) {
                $element->clearFilters();
                if ($element->getName() != 'proj') {
                    $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
                }
            }

            $this->view->assign(array(
                'kid' => $kid,
                'consultation' => $consultation,
                'form' => $form
            ));
        }
    }

    public function editAction()
    {
        $isPreview = $this->getRequest()->getPost('preview');
        if (!empty($isPreview)) {
            $this->_forward('preview');
        } else {
            $kid = $this->getRequest()->getParam('kid', 0);
            $consultation = null;
            $form = null;
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($kid);
            $aid = $this->getRequest()->getParam('aid', 0);
            if ($aid > 0) {
                $articleModel = new Model_Articles();
                $articleRow = $articleModel->find($aid)->current();
                $form = new Admin_Form_Article($kid);
                $multiOptions = array(0 => $this->view->translate('Please select…'));
                if ($kid > 0) {
                    // set multiOptions for ref_nm
                    foreach ($this->getMultioptionsByType('b') as $key => $value) {
                        $multiOptions[$key] = $value;
                    }
                    $form->getElement('ref_nm')->setMultioptions($multiOptions);
                    $form->getElement('ref_nm')->setDescription('On subpages, reference name of parent page is used.');
                } else {
                    // set multiOptions for ref_nm
                    foreach ($this->getMultioptionsByType('g') as $key => $value) {
                        $multiOptions[$key] = $value;
                    }
                    $form->getElement('ref_nm')->setMultioptions($multiOptions);
                }
                $firstLevelPages = $articleModel->getFirstLevelEntries($kid);
                $parentOptions = array(
                    0 => $this->view->translate('None')
                );
                foreach ($firstLevelPages as $page) {
                    if ($page['art_id'] != $aid) {
                        $parentOptions[$page['art_id']] = '[' . $page['art_id'] . '] ' . $page['desc'];
                    }
                }
                $form->getElement('parent_id')->setMultiOptions($parentOptions);
                $isRetFromPreview = $this->getRequest()->getPost('backFromPreview');
                if ($this->getRequest()->isPost() && empty($isRetFromPreview)) {
                    // Formular wurde abgeschickt und muss verarbeitet werden
                    $params = $this->getRequest()->getPost();
                    $params = $this->setProject($params);
                    if ($form->isValid($params)) {
                        $articleRow->setFromArray($form->getValues());
                        $articleRow->proj = implode(',', $params['proj']);
                        $articleRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
                        $articleRow->save();
                        $this->_flashMessenger->addMessage('Changes saved.', 'success');
                        $article = $articleRow->toArray();
                        $article['proj'] = explode(',', $article['proj']);
                    } else {
                        $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                        $article = $params;
                    }
                } elseif ($this->getRequest()->isPost() && !empty($isRetFromPreview)) {
                    $article = $this->getRequest()->getPost();
                    $articlePreviewForm = new Admin_Form_ArticlePreview();
                    if ($articlePreviewForm->isValid($article)) {
                        $article['proj'] = unserialize($article['proj']);
                    } else {
                        $this->_redirect('admin');
                    }
                } else {
                    $article = $articleModel->getById($aid);
                    $article['proj'] = explode(',', $article['proj']);
                }
                $form->populate($article);
            }

            foreach ($form->getElements() as $element) {
                $element->clearFilters();
                if ($element->getName() != 'proj') {
                    $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
                }
            }

            $this->view->assign(array(
                'kid' => $kid,
                'consultation' => $consultation,
                'form' => $form
            ));
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
            $nrDeleted = $articleModel->deleteById(
                $this->getRequest()->getPost('delete')
            );
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
                $articlePreviewForm = new Admin_Form_ArticlePreview();
                $data['proj'] = serialize($data['proj']);
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
                $this->view->article = $data;
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
        if (!isset($params['proj']) || empty($params['proj'])) {
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
}
