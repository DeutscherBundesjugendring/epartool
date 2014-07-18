<?php
/**
 * UserController
 *
 */
class UserController extends Zend_Controller_Action
{
    protected $_auth = null;

    protected $_flashMessenger = null;

    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_flashMessenger = $this->_helper->getHelper('flashMessenger');

        $kid = $this->getRequest()->getParam('kid', 0);
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current();
        if ($consultation) {
            $this->_consultation = $consultation;
        }
    }

    /**
     * Abmeldung
     *
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_flashMessenger->addMessage('Logout erfolgreich!', 'info');
        $this->redirect('/');
    }

    /**
     * Register
     */
    public function registerAction()
    {
        if (!$this->_request->isPost()) {
            $this->redirect('/');
        } else {
            $form = new Default_Form_Register();
            $raw_data = $this->_request->getPost();

            if (!$this->_auth->hasIdentity()) {
                $userModel = new Model_Users();

                if ($form->isValid($raw_data)) {
                    $data = $form->getValues();
                    if ($data['group_type'] != 'group') {
                        unset($data['group_specs']);
                    }

                    $confirmKey = (new Zend_Session_Namespace('inputs'))->confirmKey;
                    $userModel->getAdapter()->beginTransaction();
                    try {
                        list($uid, $isNew) = $userModel->register($data, $confirmKey);
                        $userModel->sendInputsConfirmationMail($uid, $form->getValue('kid'), $confirmKey, $isNew);
                        $userModel->getAdapter()->commit();
                    } catch (Exception $e) {
                        $userModel->getAdapter()->rollback();
                        throw $e;
                    }

                    $this->_flashMessenger->addMessage(
                        'Eine Mail zur Bestätigung der Beiträge wurde an die angegebene E-Mail-Adresse gesendet.',
                        'success'
                    );

                    $this->redirect('/');

                } else {
                    $populateForm = new Zend_Session_Namespace('populateForm');
                    $populateForm->register = serialize($form);
                    $this->_flashMessenger->addMessage('Bitte prüfe Deine Eingaben!', 'error');
                    $this->redirect('/input/confirm/kid/' . $form->getValue('kid'));
                }
            } else {
                $this->_flashMessenger->addMessage('Du bist bereits eingeloggt!', 'info');
                $this->redirect('/');
            }
        }
    }

    public function editAction()
    {
        $this->_flashMessenger->addMessage('Noch nicht implementiert!', 'info');
        $this->redirect('/');
    }

    public function inputlistAction()
    {
        $kid = isset($this->_consultation->kid) ? $this->_consultation->kid : 0;
        $consultationModel = new Model_Consultations();
        if ($this->_auth->hasIdentity()) {
            $identity = $this->_auth->getIdentity();
            if ($kid == 0) {
                $this->view->consultationList = $consultationModel->getByUser($identity->uid);
            } elseif ($kid > 0) {
                $this->view->consultation = $consultationModel->find($kid)->current();
                $inputModel = new Model_Inputs();
                $this->view->inputs = $inputModel->getUserEntriesOverview($identity->uid, $kid);
            }
        } else {
            $this->_flashMessenger->addMessage('Bitte erst anmelden!', 'error');
        }
    }

    public function passwordrecoverAction()
    {
        $form = new Default_Form_PasswordRecover();
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $userModel = new Model_Users();
                if ($userModel->recoverPassword($data['email'])) {
                    $this->_flashMessenger->addMessage('Eine E-Mail mit einem neuen Passwort wurde an die angegebene E-Mail-Adresse verschickt!', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Passwortwiederherstellung fehlgeschlagen!', 'error');
                }
                $this->redirect('/');
            } else {
                $this->_flashMessenger->addMessage('Bitte prüfe deine Eingaben!', 'error');
            }
        }
        $this->view->form = $form;
    }

    /**
     * List Users By Group
     */
    public function userlistAction()
    {
        $kid = isset($this->_consultation->kid) ? $this->_consultation->kid : 0;
        $consultationList = array();

        if (!$this->_auth->hasIdentity()) {
            $this->_flashMessenger->addMessage('Bitte erst anmelden!', 'error'); // No Login
        } else {
            $identity = $this->_auth->getIdentity();
            // No Consultation ID given list consultations
            if ($kid == 0) {
                $consultationModel = new Model_Consultations();
                $groupsModel = new Model_Votes_Groups();
                $consultationList = $consultationModel->getByUser($identity->uid)->toArray();
                foreach ($consultationList as $key => $value) {
                    $group = array();
                    $group = $groupsModel->getByConsultation($value['kid'], $identity->uid);
                    // if no group member in consultation delete consultation from array
                    if (empty($group)) {
                        unset($consultationList[$key]);
                    }
                }
                $this->view->consultationList = $consultationList;

            } elseif ($kid > 0) {
                $groupsModel = new Model_Votes_Groups();
                $consultationModel = new Model_Consultations();
                $this->view->consultationList = $consultationList;
                $group = $groupsModel->getByConsultation($kid, $identity->uid);
                // list group members
                if (count($group) > 0) {
                    $inputModel = new Model_Inputs;
                    $consultationModel = new Model_Consultations();
                    $this->view->consultation = $consultationModel->find($kid)->current();

                    $filter = array(array(
                            'field'=>'vot',
                        'operator'=>'=',
                        'value'=>'y'
                    ));
                    $this->view->inputs = $inputModel->getCountByConsultationFiltered($kid, $filter);
                    $this->view->group = $group;
                    $this->view->identity =$identity;
                } else {
                    // its possible the user reload's the page
                    $consultationList = $consultationModel->getByUser($identity->uid)->toArray();
                    foreach ($consultationList as $key => $value) {
                        $group = array();
                        $group = $groupsModel->getByConsultation($value['kid'], $identity->uid);
                        // if no group member delete consultation from array
                        if (empty($group)) {
                            unset($consultationList[$key]);
                        }
                    }
                    // all group-member deleted ?
                    empty($consultationList) ? $this->redirect('/user/userlist/') : $this->view->consultationList = $consultationList;
                }
            }
        }
    }

    /**
     * Ajaxresponse from userlistAction by click deny link
     */
    public function denyAction()
    {
        $this->_helper->layout()->disableLayout();
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }
        if (!$this->_auth->hasIdentity()) {
            exit; //no Login
        }

        $uid = $this->_request->getParam('uid', 0);
        $sub_uid = $this->_request->getParam('subuid', 0);
        $kid = isset($this->_consultation->kid) ? $this->_consultation->kid : 0;

        $votesGroupsModel = new Model_Votes_Groups();
        if ($votesGroupsModel->denyVoter($kid, $uid, $sub_uid)) {
            $user = array();
            $user['uid'] = $uid;
            $user['sub_uid'] = $sub_uid;
            $this->view->user = $user;
        } else {
            $this->view->error = 'error';
        }
    }

    /**
     * Ajaxresponse from userlistAction by click confirm link
     */
    public function confirmAction()
    {
        $this->_helper->layout()->disableLayout();
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }
        if (!$this->_auth->hasIdentity()) {
            exit; //no Login
        }

        $uid = $this->_request->getParam('uid', 0);
        $sub_uid = $this->_request->getParam('subuid', 0);
        $kid = isset($this->_consultation->kid) ? $this->_consultation->kid : 0;

        $votesGroupsModel = new Model_Votes_Groups();
        if ($votesGroupsModel->confirmVoter($kid, $uid, $sub_uid)) {
            $user = array();
            $user['uid'] = $uid;
            $user['sub_uid'] = $sub_uid;
            $this->view->user = $user;
        } else {
            $this->view->error = 'error';
        }
    }

    /**
     * Ajaxresponse from uuserlistAction by click delete link
     */
    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }
        if (!$this->_auth->hasIdentity()) {
            exit; //no Login
        }

        $uid = $this->_request->getParam('uid', 0);
        $sub_uid = $this->_request->getParam('subuid', 0);
        $kid = isset($this->_consultation->kid) ? $this->_consultation->kid : 0;
        $votesGroupsModel = new Model_Votes_Groups();
        if ($votesGroupsModel->deleteVoter($kid, $uid, $sub_uid) == 0) {
            $this->view->error = 'error';
        }
    }
}
