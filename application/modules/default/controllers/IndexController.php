<?php

class IndexController extends Zend_Controller_Action
{
    const LAST_CONSULTATION_COUNT = 3;

    private $_flashMessenger;

    public function init()
    {
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * The home page
     */
    public function indexAction()
    {
        $consModel = new Model_Consultations();
        $consCount = $consModel->fetchRow(
            $consModel
                ->select()
                ->from($consModel->info(Model_Consultations::NAME), ['count' => 'COUNT(*)'])
                ->where('public = ?', 'y')
        )
        ->count;

        $consultations = $consModel->fetchAll(
            $consModel
                ->select()
                ->where('public=?', 'y')
                ->order('ord DESC')
                ->limit(self::LAST_CONSULTATION_COUNT)
        );

        $this->view->consultations = $consultations;
        $this->view->showMoreButton = $consCount > self::LAST_CONSULTATION_COUNT;
    }

    /**
     * Perform search and display results
     */
    public function searchAction()
    {
        $needle = $this->getRequest()->getParam('q', 0);

        if ($needle) {
            $filterChain = new Zend_Filter();
            $filterChain->appendFilter(new Zend_Filter_StringTrim());
            $filterChain->appendFilter(new Zend_Filter_StringToLower(array('encoding' => 'UTF-8')));
            $filterChain->appendFilter(new Zend_Filter_HtmlEntities());
            $needle = $filterChain->filter($needle);

            $articles = new Model_Articles();
            $consultation = new Model_Consultations();
            $followUps = new Model_Followups();

            $this->view->needle = $needle;
            $this->view->resultsGeneral = $articles->search($needle);
            $this->view->resultsConsultations = $consultation->search($needle);
            $this->view->resultsFollowUps = $followUps->search($needle);
        } else {
            $this->redirect('');
        }
    }

    /**
     * Echoes a javascript object with translated messages.
     * Headers are set to application/javascript
     */
    public function i18nAction()
    {
        $i18n = [
            'Weak' => $this->view->translate('Weak'),
            'Normal' => $this->view->translate('Normal'),
            'Medium' => $this->view->translate('Medium'),
            'Strong' => $this->view->translate('Strong'),
            'Very Strong' => $this->view->translate('Very Strong'),
            'You are being logged in. Please wait...' =>
                $this->view->translate('You are being logged in. Please wait...'),
        ];

        header('Content-Type: application/javascript; charset=utf-8');
        echo 'var i18n = ' . json_encode($i18n);
        die();
    }

    public function ajaxConsultationAction()
    {
        $consModel = new Model_Consultations();
        $consultations = $consModel->fetchAll(
            $consModel
                ->select()
                ->where('public=?', 'y')
                ->order('ord DESC')
                ->limit(0, self::LAST_CONSULTATION_COUNT)
        );

        $this->view->consultations = $consultations;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->renderScript('_partials/consultationPreviews.phtml');
    }

    public function facebookAuthenticateAction()
    {
        $this->webserviceAuthenticate('Service_Webservice_Facebook');
    }

    public function googleAuthenticateAction()
    {
        $this->webserviceAuthenticate('Service_Webservice_Google');
    }

    public function facebookRegisterAction()
    {
        $this->webserviceRegister('Service_Webservice_Facebook');
    }

    public function googleRegisterAction()
    {
        $this->webserviceRegister('Service_Webservice_Google');
    }

    private function webserviceAuthenticate($webserviceClassName)
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $token = $this->getRequest()->getPost('token');
        $csrfToken = $this->getRequest()->getPost('webserviceLoginCsrf');

        $webserviceLoginSess = new Zend_Session_Namespace('webserviceLoginCsrf');
        if ($webserviceLoginSess->csrf !== $csrfToken) {
            throw new Exception('Invalid csrf token.');
        }

        try {
            $webservice = new $webserviceClassName($token);
            $user = (new Model_Users())->getByEmail($webservice->getEmail());
            if ($user) {
                $storage = Zend_Auth::getInstance()->getStorage();
                $storage->write($user);
                $this->_flashMessenger->addMessage('Login successful!', 'success');
                echo 'true';
            } else {
                echo $this->view->partial(
                    '_partials/flashMessage.phtml',
                    [
                        'message' => Zend_Registry::get('Zend_Translate')
                            ->translate('Only users who submitted a contribution can log in.'),
                        'level' => 'error'
                    ]
                );
            }
        } catch (Exception $e) {
            var_dump($e);
            $this->getResponse()->setHttpResponseCode(401);
        }
    }

    private function webserviceRegister($webserviceClassName)
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $token = $this->getRequest()->getPost('token');
        $csrfToken = $this->getRequest()->getPost('webserviceLoginCsrf');

        $webserviceLoginSess = new Zend_Session_Namespace('webserviceLoginCsrf');
        if ($webserviceLoginSess->csrf !== $csrfToken) {
            throw new Exception('Invalid csrf token.');
        }

        $userModel = new Model_Users();
        $db = $userModel->getAdapter();
        $db->beginTransaction();
        try {
            $webservice = new $webserviceClassName($token);
            $email = $webservice->getEmail();
            if ($email) {
                $user = (new Model_Users())->getByEmail($webservice->getEmail());
                if (!$user) {
                    $userArr = ['email' => $email, 'group_size' => 1];
                    $user = $userModel->createRow($userArr);
                    $user->save();
                    (new Model_User_Info())
                        ->createRow(
                            array_merge(
                                $userArr,
                                [
                                    'time_user_confirmed' => new Zend_Db_Expr('NOW()'),
                                    'date_added' => new Zend_Db_Expr('NOW()'),
                                    'kid' => (new Zend_Session_Namespace('inputs'))->kid,
                                    'uid' => $user->uid,
                                ]
                            )
                        )
                        ->save();
                    $db->commit();
                }

                $storage = Zend_Auth::getInstance()->getStorage();
                $storage->write($user);
                echo $user->email;
            } else {
                echo 'false';
            }
        } catch (Exception $e) {
            $db->rollback();
            $this->getResponse()->setHttpResponseCode(401);
        }
    }
}
