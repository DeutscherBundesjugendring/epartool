<?php
class Admin_TagController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    public function indexAction()
    {
        $tagModel = new Model_Tags();
        $tags = $tagModel->getAll();

        // Group tags by the first letter
        $tagsGrouped = array();
        $letters = array();
        $currentLetter = '';

        foreach ($tags as $tag) {
            $tagFirstLetter = self::toAscii(mb_strtoupper(mb_substr($tag['tg_de'], 0, 1, 'UTF-8')));
            if ($tagFirstLetter !== $currentLetter) {
                $currentLetter = $tagFirstLetter;
                if (!in_array($tagFirstLetter, $letters)) {
                    $letters[] = $tagFirstLetter;
                }
            }
            $tagsGrouped[$currentLetter][] = $tag;
        }
        $this->view->tags = $tagsGrouped;
        $this->view->letters = $letters;
        $this->view->form = new Admin_Form_ListControl();
        $this->view->createForm = new Admin_Form_Tag();
    }

    public function createAction()
    {
        $form = new Admin_Form_Tag();

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $tagModel = new Model_Tags();
                $key = $tagModel->add($data);
                if (!empty($key)) {
                    $this->_flashMessenger->addMessage('Neues Schlagwort angelegt.', 'success');
                }
                $this->redirect('/admin/tag/create');
            } else {
                $form->populate($data);
            }
        }
        $this->view->createForm = $form;
    }

    public function editAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            if ($this->getRequest()->getPost('delete', null)) {
                return $this->_forward('delete');
            }

            $data = $this->_request->getPost();
            $validator = new Zend_Validate_NotEmpty();
            $tagModel = new Model_Tags();
            $nrUpdated = 0;
            foreach ($data['tg_de'] as $tg_nr => $tg_de) {
                if ($tg_de != $data['tag_old'][$tg_nr]) {
                    // field was changed
                    if ($validator->isValid($tg_de)) {
                        $nr = $tagModel->updateById($tg_nr, array('tg_de' => $tg_de));
                        $nrUpdated+= $nr;
                    } else {
                        $this->_flashMessenger->addMessage('Ungültiger Wert für "'
                            . $data['tag_old'][$tg_nr] . '"!', 'error');
                    }
                }
            }
            if ($nrUpdated > 0) {
                $this->_flashMessenger->addMessage($nrUpdated . ' Einträge geändert.', 'success');
            }
        }

        $this->_redirect($this->view->url(['action' => 'index']));
    }

    /**
     * Deletes a tag
     */
    public function deleteAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $nr = (new Model_Tags())->deleteById(
                $this->getRequest()->getPost('delete')
            );
            $this->_flashMessenger->addMessage('Eintrag gelöscht.', 'success');
        }

        $this->_redirect($this->view->url(['action' => 'index']));
    }

    private static function toAscii($string)
    {
        return strtr(
            utf8_decode($string),
            utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
        );
    }
}
