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
        $this->_flashMessenger->addMessage('Logout successful!', 'info');
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
            $userModel = new Model_Users();
            $populateForm = new Zend_Session_Namespace('populateForm');

            if ($form->isValid($raw_data)) {
                unset($populateForm->register);

                $data = $form->getValues();
                if ($data['group_type'] != 'group') {
                    unset($data['group_specs']);
                }
                unset($data['group_type']);

                $confirmKey = (new Zend_Session_Namespace('inputs'))->confirmKey;
                $userModel->getAdapter()->beginTransaction();
                try {
                    if (!$this->_auth->hasIdentity()) {
                        list($uid, $isNew) = $userModel->register($data, $confirmKey);
                        $userModel->sendInputsConfirmationMail($uid, $form->getValue('kid'), $confirmKey, $isNew);
                        $this->_flashMessenger->addMessage(
                            'An email for the confimation of your contributions has been sent to your email address.',
                            'success'
                        );
                    } else {
                        $uid = $this->_auth->getIdentity()->uid;
                        $data['uid'] = $uid;
                        $userConsultModel = new Model_User_Info();
                        $userConsultRow = $userConsultModel->fetchRow(['uid=?' => $uid, 'kid=?' => $data['kid']]);
                        if ($userConsultRow) {
                            $userConsultRow->name = $data['name'];
                            $userConsultRow->age_group = $data['age_group'];
                            $userConsultRow->regio_pax = $data['regio_pax'];
                            $userConsultRow->cnslt_results = $data['cnslt_results'];
                            $userConsultRow->cmnt_ext = $data['cmnt_ext'];
                            if (isset($data['group_specs'])) {
                                $userConsultRow->source = is_array($data['group_specs']['source']) ? implode(',', $data['group_specs']['source']) : null;
                                $userConsultRow->src_misc = $data['group_specs']['src_misc'];
                                $userConsultRow->group_size = $data['group_specs']['group_size'];
                                $userConsultRow->name_group = $data['group_specs']['name_group'];
                                $userConsultRow->name_pers = $data['group_specs']['name_pers'];
                            }
                            $userConsultRow->save();
                        } else {
                            $userModel->addConsultationData($data);
                        }
                        unset($data['cmnt_ext']);
                        unset($data['kid']);
                        unset($data['csrf_token_register']);
                        if (isset($data['group_specs'])) {
                            $data = array_merge($data, $data['group_specs']);
                            unset($data['group_specs']);
                        }
                        $userModel->update($data, ['uid=?' => $uid]);
                        $this->_flashMessenger->addMessage('Your inputs have been saved.', 'success');
                    }
                    $userModel->getAdapter()->commit();
                } catch (Exception $e) {
                    $userModel->getAdapter()->rollback();
                    throw $e;
                }
                $this->redirect('/');
            } else {
                $populateForm->register = serialize($form);
                $this->_flashMessenger->addMessage('Please check your data!', 'error');
                $this->redirect('/input/confirm/kid/' . $form->getValue('kid'));
            }
        }
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
            $this->_flashMessenger->addMessage('Please log in first', 'error');
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
                    $this->_flashMessenger->addMessage('An e-mail that allows you to reset your password was sent to the provided address.', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Password reset failed!', 'error');
                }
                $this->redirect('/');
            } else {
                $this->_flashMessenger->addMessage('Please check your data!', 'error');
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
            $this->_flashMessenger->addMessage('Please log in first', 'error'); // No Login
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

        $this->view->form = new Admin_Form_ListControl();
    }

    /**
     * Performs deny, confirm and delete actions on a single particiapnt
     */
    public function participantUpdateAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $votesGroupsModel = new Model_Votes_Groups();

            if ($this->getRequest()->getPost('confirm')) {
                list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('confirm'));
                $votesGroupsModel->confirmVoter($this->_consultation->kid, $uid, $sub_uid);
                $this->_flashMessenger->addMessage('The voting participant was confirmed.', 'success');
            } elseif ($this->getRequest()->getPost('deny')) {
                list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('deny'));
                $this->_flashMessenger->addMessage('The voting participant was denied.', 'success');
                $votesGroupsModel->denyVoter($this->_consultation->kid, $uid, $sub_uid);
            } elseif ($this->getRequest()->getPost('delete')) {
                list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('delete'));
                $votesGroupsModel->deleteVoter($this->_consultation->kid, $uid, $sub_uid);
                $this->_flashMessenger->addMessage('The voting participant was deleted.', 'success');
            }
        }

        $this->_redirect($this->view->url(['action' => 'userlist']));
    }
}
