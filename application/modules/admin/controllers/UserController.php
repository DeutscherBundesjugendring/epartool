<?php
/**
 * UserController
 *
 * @desc     Users for Consultation
 */
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
        $this->_flashMessenger =
                $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * index
     * @return void
     */
    public function indexAction()
    {
        $userModel = new Model_Users();
        $this->view->userlist = $userModel->getAll();
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
                    $this->_flashMessenger->addMessage('Neue_r Nutzer_in wurde erstellt.', 'success');
                    // @todo Nutzer muss über Passwortwechsel informiert werden.
                    $this->_redirect($this->view->url(array('action' => 'index')), array('prependBase' => false));
                } else {
                    $this->_flashMessenger->addMessage(
                        'Diese E-Mail-Adresse existiert bereits! Bitte wähle eine andere.',
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
                            $url = '/admin/input/list/kid/'.$consultation["kid"].'/uid/'.$uid;
                            $label = $consultation['titl'].' ('.$consultation['count'].')';
                            $form->addElement(
                                'select',
                                'transfer_' . $consultation["kid"],
                                array(
                                    'label'=>'Beiträge übertrage: <a href="'.$url.'">'.$label.'</a>',
                                    'required'=>false,
                                    'options'=>array(0=>'...')
                                )
                            );
                            $transferOptions = array(0=>'Bitte auswählen');
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
                                    'Diese E-Mail-Adresse existiert bereits! Bitte wähle eine andere.',
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

                                $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
                                $this->_redirect('/admin/user/edit/uid/'.$uid);
//                                $form->populate($this->getRequest()->getPost());
                            }
                        } else {
                            $this->_flashMessenger->addMessage(
                                'Bitte prüfe deine Eingaben und versuche es erneut!',
                                'error'
                            );
                            $form->populate($this->getRequest()->getPost());
                        }
                    } else {
                        $form->populate($user->toArray());
                        $form->getElement('password')->setValue('');
                    }
            } else {
                $this->_flashMessenger->addMessage('Nutzer_in nicht gefunden!', 'error');
                $this->_redirect('/admin/user/index');
            }
        } else {
            $this->_flashMessenger->addMessage('Nutzer_in nicht gefunden!', 'error');
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
        $uid = $this->getRequest()->getParam('uid', 0);
        if ($uid > 0) {
            $userModel = new Model_Users();
            $userModel->getAdapter()->beginTransaction();
            try {
                $deleted = $userModel->deleteById($uid);
                $userModel->getAdapter()->commit();
            } catch (Exceptioin $e) {
                $userModel->getAdapter()->rollback();
                throw $e;
            }

            if ($deleted > 0) {
                $this->_flashMessenger->addMessage('Benutze_inr wurde gelöscht.', 'success');
            } else {
                $this->_flashMessenger->addMessage('Fehler beim Löschen von Nutzer_in. Bitte versuche es erneut.', 'error');
            }
        }
        $this->_redirect('/admin/user/index');
    }
}
