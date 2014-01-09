<?php
/**
 * IndexController
 *
 * @desc     administrationareas
 * @author                Jan Suchandt
 */
class Admin_IndexController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    /**
     * @desc Construct
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
     * @desc admin dashboard
     * @return void
     */
    public function indexAction()
    {
        $basic_settings = array(
            'editArticlesInfotexts' => array(
                'url' => array(
                    'controller' => 'article'
                ),
                'text' => 'Artikel & Infotexte bearbeiten'
            ),
            'mediaAdmin' => array(
                'url' => array(
                    'controller' => 'media'
                ),
                'text' => 'Medienverwaltung'
            ),
            'tagAdmin' => array(
                'url' => array(
                    'controller' => 'tag'
                ),
                'text' => 'Schlagwörter verwalten'
            ),
//            'userAdmin' => array(
//                'url' => array(
//                    'controller' => 'users'
//                ),
//                'text' => 'User_innen & Zugangsberechtigungen'
//            ),
//            'emailSend' => array(
//                'url' => array(
//                    'controller' => 'sendmail'
//                ),
//                'text' => 'E-Mail versenden'
//            ),
//            'emailDraft' => array(
//                'url' => array(
//                    'controller' => 'emaildraft'
//                ),
//                'text' => 'E-Mailvorlagen bearbeiten'
//            ),
//            'sentMails' => array(
//                'url' => array(
//                    'controller' => 'sentmails'
//                ),
//                'text' => 'Vom System versandte E-Mails'
//            ),
        );

        // get last 3 consultations
        $con = new Model_Consultations();
        $conList = $con->getLast();

        $inputsModel = new Model_Inputs();
        $inputs = $inputsModel->getLast(20);

        $this->view
                ->assign(
                        array(
                            'basic_settings' => $basic_settings,
                            'consultations' => $conList,
                            'inputs' => $inputs,
                        ));

    }

}
