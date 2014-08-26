<?php
/**
 * ArticleController
 *
 * @desc     Articles for Consultation
 * @author                Markus Hackel
 */
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
        // Setzen des Standardlayouts
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger =
                $this->_helper->getHelper('FlashMessenger');
        $this->initView();
        $this->_adminIndexURL = $this->view->url(array(
            'controller' => 'index',
            'action' => 'index'
        ));
    }

    /**
     * @desc show Articles Form
     * @return void
     */
    public function indexAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultation = null;
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
//            $this->_redirect($this->_adminIndexURL, array('prependBase' => false));
        }
        $this->view->articles = $articles;
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
            $refNameModel = new Model_ArticleRefNames();
            $consultation = $consultationModel->getById($kid);
            $form = new Admin_Form_Article();
            $form->setAction($this->view->baseUrl() . '/admin/article/create/kid/' . $kid);
            $multiOptions = array(0 => $this->view->translate('Please select…'));
            if ($kid > 0) {
                // set multiOptions for ref_nm
                foreach ($refNameModel->getMultioptionsByType('b') as $key => $value) {
                    $multiOptions[$key] = $value;
                }
                $form->getElement('ref_nm')->setMultioptions($multiOptions);
                $form->getElement('ref_nm')->setDescription('Bei Unterseiten gilt der Referenzname der Elternseite!');
            } else {
                // set multiOptions for ref_nm
                foreach ($refNameModel->getMultioptionsByType('g') as $key => $value) {
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
                    $articleRow->proj = implode(',', $data['proj']);
                    $newId = $articleRow->save();
                    if ($newId > 0) {
                        $this->_flashMessenger->addMessage('Neuer Artikel wurde erstellt.', 'success');
                    } else {
                        $this->_flashMessenger->addMessage('Erstellen des neuen Artikels fehlgeschlagen!', 'error');
                    }

                    $this->_redirect($this->view->url(array(
                        'action' => 'index',
                        'kid' => $kid
                    )), array('prependBase' => false));
                } else {
                    $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
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
            $refNameModel = new Model_ArticleRefNames();
            $consultation = $consultationModel->getById($kid);
            $aid = $this->getRequest()->getParam('aid', 0);
            if ($aid > 0) {
                $articleModel = new Model_Articles();
                $articleRow = $articleModel->find($aid)->current();
                $form = new Admin_Form_Article();
                $multiOptions = array(0 => $this->view->translate('Please select…'));
                if ($kid > 0) {
                    // set multiOptions for ref_nm
                    foreach ($refNameModel->getMultioptionsByType('b') as $key => $value) {
                        $multiOptions[$key] = $value;
                    }
                    $form->getElement('ref_nm')->setMultioptions($multiOptions);
                    $form->getElement('ref_nm')->setDescription('Bei Unterseiten gilt der Referenzname der Elternseite!');
                } else {
                    // set multiOptions for ref_nm
                    foreach ($refNameModel->getMultioptionsByType('g') as $key => $value) {
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
                        $articleRow->save();
                        $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
                        $article = $articleRow->toArray();
                        $article['proj'] = explode(',', $article['proj']);
                    } else {
                        $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben und versuchen Sie es erneut!', 'error');
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

    public function deleteAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $aid = $this->getRequest()->getParam('aid', 0);
        if ($aid > 0) {
            $articleModel = new Model_Articles();
            $articleRow = $articleModel->getById($aid);
            if ($articleRow['kid'] == $kid) {
                $nrDeleted = $articleModel->deleteById($aid);
                if ($nrDeleted > 0) {
                    $this->_flashMessenger->addMessage('Der Artikel wurde gelöscht.', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Artikel konnte nicht gelöscht werden. Eventuell existieren Unterseiten. Dann bitte zuerst diese löschen!', 'error');
                }
            }
        }
        $this->_redirect('/admin/article/index/kid/' . $kid);
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
                $prevView->addScriptPath('application/modules/default/views/scripts');
                $prevView->addHelperPath('application/modules/default/views/helpers');
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
}
