<?php

class Admin_UserController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    /**
     * Construct
     * @return void
     */
    public function init()
    {
        // Setzen des Standardlayouts
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * index
     * @return void
     */
    public function indexAction()
    {
        $userModel = new Model_Users();
        $this->view->userlist = $userModel->getAll();
        $this->view->form = new Admin_Form_ListControl();
    }

    public function createAction()
    {
        $form = new Admin_Form_User_Create();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $userModel = new Model_Users();
                // check if email allready exists
                $emailAddress =$form->getValue('email');
                if (!$userModel->emailExists($emailAddress)) {
                    $userRow = $userModel->createRow($form->getValues());
                    $userPasswort = $form->getValue('pwd');
                    if (!empty($userPasswort)) {
                        $userRow->pwd = md5($userPasswort);
                    }
                    $userRow->save();
                    $this->_flashMessenger->addMessage('New user has been successfully added.', 'success');
                    // @todo Nutzer muss über Passwortwechsel informiert werden.
                    $this->_redirect($this->view->url(array('action' => 'index')), array('prependBase' => false));
                } else {
                    $this->_flashMessenger->addMessage(
                        'User with this email address already exists.',
                        'error'
                    );
                    $form->populate($form->getValues());
                }

            } else {
                $form->populate($form->getValues());
            }
        }

        $this->view->assign(array('form' => $form));
    }

    public function editAction()
    {
        $uid = $this->getRequest()->getParam('uid', 0);
        $this->view->uid = $uid;
        if ($uid > 0) {
            $userModel = new Model_Users();
            $inputModel = new Model_Inputs();
            $user = $userModel->getById($uid);
            if (!empty($user)) {
                    $form = new Admin_Form_User_Edit();
                    $form->setAction($this->view->baseUrl() . '/admin/user/edit/uid/' . $uid);
                    // remove transfer if user has no input
                    $countInputByUser = $inputModel->getCountByUser($uid);

                    $consultationModel = new Model_Consultations();
                    if ($countInputByUser<1) {
                        $transerElement = $form->removeElement('transfer');
                    } else {
                        // generate selects for every consultation
                        $consultations = $consultationModel->getByUser($uid);
                        foreach ($consultations AS $consultation) {
                            $url = '/admin/input/list-by-user/kid/'.$consultation["kid"].'/uid/'.$uid;
                            $label = $consultation['titl'].' ('.$consultation['count'].')';
                            $form->addElement(
                                'select',
                                'transfer_' . $consultation["kid"],
                                array(
                                    'label'=>'Transfer contributions from: <a href="'.$url.'" target="_blank">'.$label.'</a>',
                                    'required'=>false,
                                    'options'=>array(0 => '…')
                                )
                            );
                            $transferOptions = array(0 => 'Please select');
                            $users = $userModel->getAllConfirmed();
                            foreach ($users As $tmpuser) {
                                if (!empty($tmpuser['email'])) {
                                    $transferOptions[$tmpuser['uid']] = $tmpuser['email'];
                                }
                            }
                            $form->getElement('transfer_' . $consultation["kid"])->setMultioptions($transferOptions);
                            $form
                                ->getElement('transfer_' . $consultation["kid"])
                                ->getDecorator('BootstrapStandard')
                                ->setOption('escapeLabel', false);
                        }
                    }
                    if ($this->getRequest()->isPost()) {
                        $params = $this->getRequest()->getPost();
                        if ($this->getRequest()->getPost('password')) {
                            $form->getElement('password_confirm')->setRequired(true);
                        }
                        if ($form->isValid($params)) {
                            $emailAddress =$form->getValue('email');
                            if ($user->email != $emailAddress && $userModel->emailExists($emailAddress)) {
                                $this->_flashMessenger->addMessage(
                                    'User with this email address already exists.',
                                    'error'
                                );
                                $params = $this->getRequest()->getPost();
                                $params['email'] = $user->email;
                                $form->populate($params);
                            } else {
                                $row = $userModel->find($uid)->current();
                                $values = $form->getValues();
                                unset($values['password_confirm']);
                                if ($values['password']) {
                                    $values['password'] = $userModel->hashPassword( $values['password']);
                                } else {
                                    unset($values['password']);
                                }
                                $row->setFromArray($values);
                                $row->save();

                                // transfer userinputs
                                $consultations = $consultationModel->getByUser($uid);
                                foreach ($consultations AS $consultation) {
                                    if (!empty($params['transfer_'. $consultation["kid"]])) {
                                        $inputModel->transferInputs(
                                            $uid,
                                            $params['transfer_'. $consultation["kid"]],
                                            $consultation["kid"]
                                        );
                                    }
                                }

                                $this->_flashMessenger->addMessage('Changes savedt.', 'success');
                                $this->_redirect('/admin/user/edit/uid/'.$uid);
//                                $form->populate($this->getRequest()->getPost());
                            }
                        } else {
                            $this->_flashMessenger->addMessage(
                                'Form is not valid.',
                                'error'
                            );
                            $form->populate($this->getRequest()->getPost());
                        }
                    } else {
                        $form->populate($user->toArray());
                        $form->getElement('password')->setValue('');
                    }
            } else {
                $this->_flashMessenger->addMessage('User not found.', 'error');
                $this->_redirect('/admin/user/index');
            }
        } else {
            $this->_flashMessenger->addMessage('User not found.', 'error');
            $this->_redirect('/admin/user/index');
        }

        $this->view->assign(
            array(
                'user' => $user,
                'form' => $form
            )
        );
    }

    public function deleteAction()
    {
       $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $userModel = new Model_Users();
            $userModel->getAdapter()->beginTransaction();
            try {
                $deleted = $userModel->deleteById($this->getRequest()->getPost('delete'));
                $userModel->getAdapter()->commit();
                $this->_flashMessenger->addMessage('The user was deleted.', 'success');
            } catch (Exceptioin $e) {
                $userModel->getAdapter()->rollback();
                throw $e;
            }
        }

        $this->_redirect($this->view->url(['action' => 'index']));
    }
}
