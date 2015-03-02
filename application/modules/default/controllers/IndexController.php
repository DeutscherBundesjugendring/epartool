<?php

use \Facebook\FacebookSession;
use \Facebook\FacebookRequest;
use \Facebook\FacebookAuthorizationException;

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

    public function authenticateWithFacebookAction()
    {
        $accessToken = $this->getRequest()->getPost('accessToken');
        $csrfToken = $this->getRequest()->getPost('webserviceLoginCsrf');

        $webserviceLoginSess = new Zend_Session_Namespace('webserviceLoginCsrf');
        if ($webserviceLoginSess->csrf !== $csrfToken) {
            throw new Exception('Invalid csrf token.');
        }

        $facebookConf = Zend_Registry::get('systemconfig')->webservice->facebook;
        FacebookSession::setDefaultApplication($facebookConf->appId, $facebookConf->appSecret);
        $session = new FacebookSession($accessToken);
        $request = new FacebookRequest($session, 'GET', '/me');
        try {
            $response = $request->execute()->getGraphObject();
            $user = (new Model_Users())->getByEmail($response->getProperty('email'));
            $storage = Zend_Auth::getInstance()->getStorage();
            $storage->write($user);
            $this->_flashMessenger->addMessage('Login successful!', 'success');
            die('true');
        } catch (FacebookAuthorizationException $e) {
            $this->getResponse()->setHttpResponseCode(401);
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
        }
    }

    public function authenticateWithGoogleAction()
    {
        $authCode = $this->getRequest()->getPost('authCode');
        $csrfToken = $this->getRequest()->getPost('webserviceLoginCsrf');

        $webserviceLoginSess = new Zend_Session_Namespace('webserviceLoginCsrf');
        if ($webserviceLoginSess->csrf !== $csrfToken) {
            throw new Exception('Invalid csrf token.');
        }

        $googleConf = Zend_Registry::get('systemconfig')->webservice->google;
        $client = new Google_Client();
        $client->setClientId($googleConf->clientId);
        $client->setClientSecret($googleConf->clientSecret);
        $client->setRedirectUri('postmessage');
        $client->setScopes('email', 'profile');
        $client->authenticate($authCode);

        $token = json_decode($client->getAccessToken())->access_token;
        $req = new Google_Http_Request('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $token);
        $tokenInfo = json_decode($client->getAuth()->authenticatedRequest($req)->getResponseBody());

        if (!empty($tokenInfo->error)) {
            throw new Exception($tokenInfo->error);
        }

        if ($tokenInfo->audience !== Zend_Registry::get('systemconfig')->webservice->google->clientId) {
            $this->getResponse()->setHttpResponseCode(401);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }

        $user = (new Model_Users())->getByEmail($tokenInfo->email);
        $storage = Zend_Auth::getInstance()->getStorage();
        $storage->write($user);
        $this->_flashMessenger->addMessage('Login successful!', 'success');
        die('true');
    }
}
