<?php
/**
 * Plugin für Access Control
 * @author Markus
 *
 */
class Plugin_Auth_AccessControl extends Zend_Controller_Plugin_Abstract
{
    protected $_auth = null;

    protected $_acl = null;

    protected $_flashMessenger = null;

    public function __construct(Zend_Auth $auth, Zend_Acl $acl)
    {
        $this->_auth = $auth;
        $this->_acl    = $acl;
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$this->_auth->hasIdentity()
            && null !== $request->getPost('username')
            && null !== $request->getPost('password')
        ) {
            $form = new Default_Form_Login();
            if ($form->isValid($request->getPost())) {
                $filter = new Zend_Filter_StripTags();
                $username = $filter->filter($request->getPost('username'));
                $password = $filter->filter($request->getPost('password'));
                if (empty($username)) {
                    $this->_flashMessenger->addMessage('Please insert user name!', 'error');
                } elseif (empty($password)) {
                    $this->_flashMessenger->addMessage('Please insert password!', 'error');
                } else {
                    $authAdapter = new Plugin_Auth_AuthAdapter($username, $password);
                    $result = $this->_auth->authenticate($authAdapter);
                    if (!$result->isValid()) {
                        $messages = $result->getMessages();
                        $message = $messages[0];
                        $this->_flashMessenger->addMessage($message, 'error');
                    } else {
                        $storage = $this->_auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject());
                        $this->_flashMessenger->addMessage('Login successful!', 'success');
                    }
                }
            } else {
                $this->_flashMessenger->addMessage('Login failed!', 'error');
            }
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_auth->hasIdentity() && is_object($this->_auth->getIdentity())) {
            $identity = $this->_auth->getIdentity();
            if (!$this->isIdentityValid($identity->toArray())) {
                $this->_auth->clearIdentity();
                $this->_flashMessenger->addMessage('Please log in first!', 'info');
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrl('/');
            }
            $role = $identity->role;
            $userModel = new Model_Users();
            $userModel->ping($identity->uid);
        } else {
            $role = 'guest';
        }

        $module         = $request->getModuleName();
        // Ressourcen = Modul -> kann hier geändert werden!
        $resource     = $module;
        if (!$this->_acl->has($resource)) {
            $resource = null;
        }
        if (!$this->_acl->isAllowed($role, $resource)) {
            if ($this->_auth->hasIdentity()) {
                // angemeldet, aber keine Rechte -> Fehler!
                $request->setModuleName('default');
//                $request->setControllerName('error');
//                $request->setActionName('noAccess');
                $request->setControllerName('index');
                $request->setActionName('index');
                $this->_flashMessenger->addMessage('No admission rights for this page!', 'error');
            } else {
                //nicht angemeldet -> Login
                $this->_flashMessenger->addMessage('Please log in first!', 'info');
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrl('/');
            }
        }
    }

    /**
     * @param array $identity
     * @return bool
     */
    private function isIdentityValid(array $identity)
    {
        $row = (new Model_Users)->find($identity['uid'])->current();
        if ($row === null) {
            return false;
        }
        $userIdentity = $row->toArray();
        foreach ($identity as $key => $value) {
            if (!array_key_exists($key, $userIdentity)) {
                return false;
            }
        }

        return true;
    }
}
