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
            'All age groups' => $this->view->translate('All age groups'),
            'Back to timeline' => $this->view->translate('Back to timeline'),
            'Click here to explain contribution' => $this->view->translate('Click here to explain contribution'),
            'Download' => $this->view->translate('Download'),
            'Follow path' => $this->view->translate('Follow path'),
            'Loading…' => $this->view->translate('Loading…'),
            'Medium' => $this->view->translate('Medium'),
            'Normal' => $this->view->translate('Normal'),
            'Shut back' => $this->view->translate('Shut back'),
            'Something went wrong' => $this->view->translate('Something went wrong'),
            'Strong' => $this->view->translate('Strong'),
            'Using the superbutton is not allowed.' => $this->view->translate('Using of superbutton is not allowed.'),
            'Very Strong' => $this->view->translate('Very Strong'),
            'Weak' => $this->view->translate('Weak'),
            'You are being logged in. Please wait…' => $this->view->translate(
                'You are being logged in. Please wait…'
            ),
            'Your contributions have not been saved' => $this->view->translate(
                'Your contributions have not been saved.'
            ),
            'supporters' => $this->view->translate('supporters'),
        ];

        header('Content-Type: application/javascript; charset=utf-8');
        // @codingStandardsIgnoreLine
        echo 'var i18n = ' . json_encode($i18n, JSON_UNESCAPED_UNICODE);
        // @codingStandardsIgnoreLine
        die();
    }

    public function customCssAction()
    {
        $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();
        $theme = $project;
        if (!empty($project['theme_id'])) {
            $theme = (new Model_Theme())->find($project['theme_id'])->current();
        }

        $css = $this->view->partial('index/custom-css.phtml', [
            'colorPrimary' => $theme['color_primary'],
            'colorAccent1' => $theme['color_accent_1'],
            'colorAccent2' => $theme['color_accent_2'],
        ]);

        header('Content-Type: text/css; charset=utf-8');
        // @codingStandardsIgnoreLine
        echo $css;
        // @codingStandardsIgnoreLine
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
                // @codingStandardsIgnoreLine
                echo 'true';
            } else {
                // @codingStandardsIgnoreLine
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
                    $consultationId = (new Zend_Session_Namespace('inputs'))->kid;
                    $userArr = [
                        'email' => $email,
                        'group_size' => (new Model_GroupSize())->getInitGroupSize($consultationId)['id'],
                    ];
                    $user = $userModel->createRow($userArr);
                    $user->save();
                    (new Model_User_Info())
                        ->createRow(
                            array_merge(
                                $userArr,
                                [
                                    'time_user_confirmed' => new Zend_Db_Expr('NOW()'),
                                    'date_added' => new Zend_Db_Expr('NOW()'),
                                    'kid' => $consultationId,
                                    'uid' => $user->uid,
                                ]
                            )
                        )
                        ->save();
                    $db->commit();
                }

                $storage = Zend_Auth::getInstance()->getStorage();
                $storage->write($user);
                // @codingStandardsIgnoreLine
                echo $user->email;
            } else {
                // @codingStandardsIgnoreLine
                echo 'false';
            }
        } catch (Exception $e) {
            $db->rollback();
            $this->getResponse()->setHttpResponseCode(401);
        }
    }
}
