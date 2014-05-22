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
                    // JSU array of consultation which this user has joint
                    $this->view->inputsConsultation = $inputModel->getCountByUserGroupedConsultation($uid);

                    $consultationModel = new Model_Consultations();
                    if ($countInputByUser<1) {
                        $transerElement = $form->removeElement('transfer');
                    } else {
                        // generate selects for every consultation
                        $consultations = $consultationModel->getByUserinputs($uid);
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
                                ->getDecorator('Label')
                                ->setOption('escape', false);

                        }
                    }
                    if ($this->getRequest()->isPost()) {
                        $params = $this->getRequest()->getPost();
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
                                $row->setFromArray($form->getValues());
                                $userPasswort = $form->getValue('newpassword');
                                if (!empty($userPasswort)) {
                                    /**************************/
                                    throw new Exception('UNSUPORTED!! Admin should never know user password!');
                                    /**************************/
                                    $row->pwd = md5($userPasswort);
                                    // if send e-mail with new pwd to user
                                    $emailsend = $form->getValue('pwdsend');
                                    if ($emailsend === '1') {
                                        $emailModel = new Model_Emails();
                                        $emailSuccess = $emailModel->send(
                                            $params['email'],
                                            'Passwort-Aktualisierung',
                                            'Dein Passwort wurde aktualisiert. Das neue Passwort lautet: ' . $userPasswort,
                                            'pwdalter',
                                            array(
                                             '{{USER}}'=>$params['name'],
                                             '{{EMAIL}}'=>$params['email'],
                                             '{{PWD}}' =>$userPasswort
                                            )
                                        );
                                    }

                                }
                                $row->save();

                                // transfer userinputs
                                $consultations = $consultationModel->getByUserinputs($uid);
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
            $deleted = $userModel->deleteById($uid);
            //$this->_helper->layout()->disableLayout();
            //$this->_helper->viewRenderer->setNoRender(true);
            if ($deleted > 0) {
                $this->_flashMessenger->addMessage('Benutze_inr wurde gelöscht.', 'success');
            } else {
                $this->_flashMessenger->addMessage('Fehler beim Löschen von Nutzer_in. Bitte versuche es erneut.', 'error');
            }
        }
        $this->_redirect('/admin/user/index');
    }
}
