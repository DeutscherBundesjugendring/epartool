<?php
/**
 * ConsultationController
 *
 * @desc     administrationareas
 * @author                Markus Hackel
 */

class Admin_ConsultationController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;

    /**
     * @desc Construct
     * @return void
     */
    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();

        $kid = $this->getRequest()->getParam('kid');
        if ($kid) {
            $consultationModel = new Model_Consultations();
            $this->_consultation = $consultationModel->find($kid)->current();
            $this->view->consultation = $this->_consultation;
        }
    }

    /**
     * @desc consultation votingprepare
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * create new Consultation
     *
     */
    public function newAction()
    {
        $form = new Admin_Form_Consultation();

        if ($this->getRequest()->isPost()
                && false !== $this->getRequest()->getPost('submit', false)) {
                    $consultationModel = new Model_Consultations();
                    if ($form->isValid($this->getRequest()->getPost())) {
                        $consultationRow = $consultationModel->createRow($form->getValues());
                        $consultationRow->proj = implode(',', $form->getElement('proj')->getValue());
                        $newId = $consultationRow->save();
                        if ($newId > 0) {
                            $this->_flashMessenger->addMessage('Neue Beteiligungsrunde wurde erstellt.', 'success');
                            $this->_redirect('/admin/consultation/edit/kid/' . $consultationRow->kid);
                        } else {
                            $this->_flashMessenger->addMessage('Erstellen der neuen Beteiligungsrunde fehlgeschlagen!', 'error');
                        }
                    } else {
                        $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben!', 'error');
                        $form->populate($this->getRequest()->getPost());
                    }
        }

        foreach ($form->getElements() as $element) {
            $element->clearFilters();
            if ($element->getName() != 'proj') {
                $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
            }
        }

        $this->view->form = $form;
    }

    /**
     * edit Consultation settings
     *
     */
    public function editAction()
    {
        $form = new Admin_Form_Consultation();
        $form->setAction($this->view->baseUrl() . '/admin/consultation/edit/kid/' . $this->_consultation->kid);
        $form->addDecorator('cancelLink', ['url' => '/']);

        if ($this->getRequest()->isPost()
                && false !== $this->getRequest()->getPost('submit', false)) {
                    // if date-inputs not checked, remove validators and set default values
                    $posts = $this->getRequest()->getPost();
                    if ($posts['inp_show'] === 'n') {
                        Zend_Debug::dump('Remove valids');
                        $form->getElement('inp_fr')->removeValidator('NotEmpty');
                        $form->getElement('inp_fr')->removeValidator('Date');
                        $form->getElement('inp_fr')->setOptions(array('required'=>false));
                        $form->getElement('inp_to')->removeValidator('NotEmpty');
                        $form->getElement('inp_to')->removeValidator('Date');
                        $form->getElement('inp_to')->setOptions(array('required'=>false));
                    }
                    if ($posts['spprt_show'] === 'n') {
                        Zend_Debug::dump('Remove valids');
                        $form->getElement('spprt_fr')->removeValidator('NotEmpty');
                        $form->getElement('spprt_fr')->removeValidator('Date');
                        $form->getElement('spprt_fr')->setOptions(array('required'=>false));
                        $form->getElement('spprt_to')->removeValidator('NotEmpty');
                        $form->getElement('spprt_to')->removeValidator('Date');
                        $form->getElement('spprt_to')->setOptions(array('required'=>false));
                    }
                    if ($posts['vot_show'] === 'n') {
                        Zend_Debug::dump('Remove valids');
                        $form->getElement('vot_fr')->removeValidator('NotEmpty');
                        $form->getElement('vot_fr')->removeValidator('Date');
                        $form->getElement('vot_fr')->setOptions(array('required'=>false));
                        $form->getElement('vot_to')->removeValidator('NotEmpty');
                        $form->getElement('vot_to')->removeValidator('Date');
                        $form->getElement('vot_to')->setOptions(array('required'=>false));
                    }
                    if ($form->isValid($this->getRequest()->getPost())) {
                        $this->_consultation->setFromArray($form->getValues());
                        $this->_consultation->proj = implode(',', $form->getElement('proj')->getValue());
                        $this->_consultation->discussion_from = $this->_consultation->discussion_from ? $this->_consultation->discussion_from : null;
                        $this->_consultation->discussion_to = $this->_consultation->discussion_to ? $this->_consultation->discussion_to : null;
                        $this->_consultation->save();
                        $this->_flashMessenger->addMessage('Änderungen gespeichert.', 'success');

                        $this->_redirect('/admin/consultation/edit/kid/' . $this->_consultation->kid);
                    } else {
                        $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben!', 'error');
                        $form->populate($form->getValues());
                    }
        } else {
            $form->populate($this->_consultation->toArray());
            $form->getElement('proj')->setValue(explode(',', $this->_consultation['proj']));
        }

        foreach ($form->getElements() as $element) {
            $element->clearFilters();
            if ($element->getName() != 'proj') {
                $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
            }
        }

        $this->view->form = $form;
    }

    /**
     * statistical Report
     *
     */
    public function reportAction()
    {
        $kid = $this->_request->getParam('kid', 0);
        if (empty($kid)) {
            $this->_flashMessenger->addMessage('Keine Beteiligungsrunde angegeben!', 'error');
            $this->redirect('/admin');
        }
        $inputsModel = new Model_Inputs();
        $questionModel = new Model_Questions();

        $questionRowset = $questionModel->getByConsultation($kid);
        $questions = array();
        foreach ($questionRowset as $question) {
            $question = $question->toArray();
            $questions[$question['qi']] = $question;
            $questions[$question['qi']]['nrInputsConfirmed'] = $inputsModel
                ->getCountByQuestionFiltered($question['qi'], array(
                    array(
                        'field' => 'user_conf',
                        'operator' => '=',
                        'value' => 'c'
                    )
                ));
            $questions[$question['qi']]['nrInputsVoting'] = $inputsModel
                ->getCountByQuestionFiltered($question['qi'], array(
                    array(
                        'field' => 'vot',
                        'operator' => '=',
                        'value' => 'y'
                    )
                ));
        }

        $votesIndivModel = new Model_Votes_Individual();
        $votesRightsModel = new Model_Votes_Rights();

        $this->view->assign(array(
            'nrParticipants' => $inputsModel->getCountParticipantsByConsultation($kid),
            'nrInputs' => $inputsModel->getCountByConsultation($kid, false),
            'nrInputsConfirmed' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'user_conf', 'operator' => '=', 'value' => 'c'))),
            'nrInputsUnconfirmed' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'user_conf', 'operator' => '=', 'value' => 'u'))),
            'nrInputsBlocked' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'block', 'operator' => '=', 'value' => 'y'))),
            'nrInputsVoting' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'vot', 'operator' => '=', 'value' => 'y'))),
            'questions' => $questions,
            'votingCountIndiv' => $votesIndivModel->getCountByConsultation($kid),
            'weightCounts' => $votesRightsModel->getWeightCountsByConsultation($kid)
        ));
    }

    /**
     * Ajax-Delete Action (no view)
     * Need param integer kid in request-object
     */
    public function deleteAction()
    {
        // Deaktiviere Layout und View
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $return = array(
            'success'=>true,
            'message'=>'',
            'params'=>array(),
            'return'=>array()
        );

        $params = $this->getRequest()->getParams();
        $return['params'] = $params;

        // Validation userlevel
        $current_user = Zend_Auth::getInstance()->getIdentity();
        if ($current_user->lvl !== 'adm') {
            $return['success'] = false;
            $return['message'] = 'ungültige Beteiligungsrunde';
        }

        // Validation kid
        if (empty($params['kid'])) {
            $return['success'] = false;
            $return['message'] = 'ungültige Beteiligungsrunde';
        }

        // Validation consultation exists
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->getById($params['kid']);
        if (!$consultation) {
            $return['success'] = false;
            $return['message'] = 'Beteiligungsrunde nicht gefunden';
        }

        // Validation successful
        if ($return['success']) {
            $kid = $params['kid'];

            // Delete articles by consultation
            $articleModel = new Model_Articles();
            $articles = $articleModel->getByConsultation($kid);
            if ($articles) {
                foreach ($articles As $article) {
                    $articleModel->deleteById($article['art_id']);
                }
            }

            // Delete Consultation
            $consultationModel->deleteById($kid);

        }

        $this->_redirect('/admin/consultation/index/');
    }
}
