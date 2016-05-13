<?php

class Admin_UserController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

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
                    $this->_flashMessenger->addMessage('User with this email address already exists.', 'error');
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
        $uid = $this->getRequest()->getParam('uid');
        $userModel = new Model_Users();
        $user = $userModel->getById($uid);

        if (!$user) {
            $this->_flashMessenger->addMessage('User not found.', 'error');
            $this->_redirect('/admin/user/index');
        }

        $form = new Admin_Form_User_Edit();

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
                $values = $form->getValues();
                if ($values['password']) {
                    $values['password'] = $userModel->hashPassword($values['password']);
                } else {
                    unset($values['password']);
                }
                $user->setFromArray($values);
                $user->save();
                $this->_flashMessenger->addMessage('Changes saved.', 'success');
                $this->_redirect('/admin/user/edit/uid/' . $uid);
            } else {
                $this->_flashMessenger->addMessage(
                    'Form is not valid, please check the values entered.',
                    'error'
                );
            }
        } else {
            $form->populate($user->toArray());
            $form->getElement('password')->setValue('');
        }

        $this->view->user = $user;
        $this->view->form = $form;
    }

    public function transferContributionsAction()
    {
        $uid = $this->getRequest()->getParam('uid');
        $userModel = new Model_Users();
        $user = $userModel->getById($uid);

        if (!$user) {
            $this->_flashMessenger->addMessage('User not found.', 'error');
            $this->_redirect('/admin/user/index');
        }

        $inputModel = new Model_Inputs();
        $consultationModel = new Model_Consultations();
        $form = new Admin_Form_User_TransferContributions();

        $consultations = $consultationModel->getByUser($uid);
        foreach ($consultations as $i => $consultation) {
            $url = '/admin/input/list-by-user/kid/' . $consultation["kid"] . '/uid/' . $uid;
            $label = $consultation['titl'] . ' (' . $consultation['count'] . ')';
            $form->addElement(
                'select',
                'transfer_' . $consultation["kid"],
                [
                    'label' => 'Transfer contributions from: <a href="'
                        . $url . '" target="_blank">' . $label . '</a>',
                    'required' => false,
                    'options' => array(0 => '…'),
                    'order' => $i,
                ]
            );
            $transferOptions = array(0 => 'Please select');
            $users = $userModel->getAllConfirmed();
            foreach ($users as $tmpuser) {
                $transferOptions[$tmpuser['uid']] = '';
                if ($tmpuser['name'] !== null) {
                    $transferOptions[$tmpuser['uid']] .= $tmpuser['name'];
                }
                if (!empty($tmpuser['email'])) {
                    if (!empty($transferOptions[$tmpuser['uid']])) {
                        $transferOptions[$tmpuser['uid']] .= ' <' . $tmpuser['email'] . '>';
                    } else {
                        $transferOptions[$tmpuser['uid']] .= $tmpuser['email'];
                    }
                }
            }
            $form->getElement('transfer_' . $consultation["kid"])->setMultioptions($transferOptions);
            $form
                ->getElement('transfer_' . $consultation["kid"])
                ->getDecorator('BootstrapStandard')
                ->setOption('escapeLabel', false);
        }

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            foreach ($consultations as $consultation) {
                if (!empty($params['transfer_'. $consultation['kid']])) {
                    $inputModel->transferInputs(
                        $uid,
                        $params['transfer_'. $consultation['kid']],
                        $consultation['kid']
                    );
                }
            }

            $this->_flashMessenger->addMessage('Contributions transferred.', 'success');
            $this->_redirect('/admin/user/transfer-contributions/uid/' . $uid);
        }

        $this->view->form = $form;
        $this->view->user = $user;
        $this->view->lastUserContribution = $inputModel->fetchRow(
            $inputModel
                ->select('when')
                ->where('uid=?', $user['uid'])
                ->order('when DESC')
                ->limit(1)
        )->when;
    }

    public function deleteAction()
    {
       $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $userModel = new Model_Users();
            $userModel->getAdapter()->beginTransaction();
            try {
                $userModel->deleteById($this->getRequest()->getPost('delete'));
                $userModel->getAdapter()->commit();
                $this->_flashMessenger->addMessage('User has been deleted.', 'success');
            } catch (Exceptioin $e) {
                $userModel->getAdapter()->rollback();
                throw $e;
            }
        }

        $this->_redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
    }
}
