<?php

/**
 * MediaController
 *
 * @desc   Media Admin
 * @author        Markus Hackel
 */
class Admin_MediaController extends Zend_Controller_Action
{
    /**
     * FlashMessenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
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
        $this->initView();
    }

    /**
     * @desc media dashboard
     * @return void
     */
    public function indexAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultation = null;
        $directory = MEDIA_PATH;
        $dirWs = $this->view->baseUrl() . '/media';
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->find($kid)->current();
            if ($consultation) {
                $directory.= '/consultations/' . $kid;
                $dirWs.= '/consultations/' . $kid;
                if (!is_dir($directory)) {
                    mkdir($directory);
                }
            }
        } else {
            $directory.= '/misc';
            $dirWs.= '/misc';
        }
        $files = scandir($directory);
        $action = $this->view->url(
            array(
                'action' => 'delete',
                'kid' => $kid
            )
        );
        $i = 0;
        $aFileinfo = array();
        if (!empty($files)) {
            foreach ($files as $filename) {
                if (is_file($directory . '/' . $filename)) {
                    $i++;
                    $deleteForm = (new Admin_Form_Media_Delete())
                        ->setAction($action)
                        ->setAttrib('name', 'delete_' . $i)
                        ->setAttrib('id', 'delete_' . $i);
                    $deleteForm->addCsrfHash('csrf_token_mediadelete_' . $i);
                    $deleteForm->addElement(
                        $deleteForm
                            ->createElement('hidden', 'form_num')
                            ->setDecorators(array('ViewHelper'))
                            ->setValue($i));
                    $deleteForm
                        ->getElement('file')
                        ->setValue($filename);
                    $aFileinfo[$filename] = pathinfo($directory . '/' . $filename);
                    $aFileinfo[$filename]['size'] = ceil(filesize($directory . '/' . $filename) / 1024);
                    $aFileinfo[$filename]['deleteform'] = $deleteForm;
                }
            }
        }
        $form = new Admin_Form_Media_Upload();
        $form->setAction(
            $this->view->url(
                array(
                    'action' => 'upload',
                    'kid' => $kid
                )
            )
        );

        $this->view->assign(
            array(
                'kid' => $kid,
                'consultation' => $consultation,
                'directory' => $dirWs,
                'files' => $aFileinfo,
                'form' => $form
            )
        );
    }

    /**
     * Handles Upload Action, redirects to index view
     */
    public function uploadAction()
    {
        $kid = (int) $this->getRequest()->getParam('kid', 0);
        $uploadScenario = $this->getRequest()->getParam('upload_scenario', null);
        $form = new Admin_Form_Media_Upload();

        if ($this->getRequest()->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                $originalFilename = pathinfo($form->file->getFileName());
                if ($kid > 0) {
                    $uploadDir = realpath(MEDIA_PATH . '/consultations/' . $kid);
                } else {
                    $uploadDir = realpath(MEDIA_PATH . '/misc');
                }
                $uploadFilename = $uploadDir . '/' . $originalFilename['basename'];

                if (is_dir($uploadDir)) {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $upload->addFilter(
                        'Rename',
                        array(
                            'target' => $uploadFilename,
                            'overwrite' => true
                        )
                    );
                    try {
                        if ($upload->receive()) {
                            if ($uploadScenario === Model_FollowupFiles::UPLOAD_SCENARIO_THUMB) {
                                $sizes = Zend_Registry::get('systemconfig')->image->sizes->followupThumb->toArray();
                                $image = new Dbjr_File_Image();
                                $image
                                    ->setImage($uploadFilename)
                                    ->resize($sizes['width'], $sizes['height']);
                            }
                            $this->_flashMessenger->addMessage(
                                'Die Datei »' . $originalFilename['basename'] . '« wurde erfolgreich hinzugefügt.',
                                'success'
                            );
                        } else {
                            $this
                                ->_flashMessenger
                                ->addMessage(
                                    'Die Datei konnte nicht hinzugefügt werden. Sie war möglicherweise zu groß oder die Schreibrechte nicht ausreichend.',
                                    'error'
                                );
                        }
                    } catch (Zend_File_Transfer_Exception $e) {
                        $this->_flashMessenger->addMessage($e->getMessage(), 'error');
                    }
                }
            } else {
                $this->_flashMessenger->addMessage('Upload fehlgeschlagen.', 'error');
            }
        }

        $this->view->form = $form;
    }

    /**
     * @desc Handles Delete Action, redirects to index view
     * @return void
     */
    public function deleteAction()
    {
        $kid = (int) $this->getRequest()->getParam('kid', 0);

        $formData = $this->_request->getParams();
        $form = new Admin_Form_Media_Delete();
        $form->addCsrfHash('csrf_token_mediadelete_' . $formData['form_num']);
        if ($form->isValid($formData)) {
            $originalFilename = $form->getElement('file')->getValue();

            if ($kid > 0) {
                $deleteDir = realpath(MEDIA_PATH . '/consultations/' . $kid);
            } else {
                $deleteDir = realpath(MEDIA_PATH. '/misc');
            }
            $deleteFilename = $deleteDir . '/' . $originalFilename;

            if (is_file($deleteFilename)) {
                if (unlink($deleteFilename)) {
                    $this
                        ->_flashMessenger
                        ->addMessage('Die Datei »' . $originalFilename . '« wurde erfolgreich gelöscht.', 'success');
                } else {
                    $this->_flashMessenger
                            ->addMessage('Datei konnte nicht gelöscht werden.', 'error');
                }
            } else {
                $this->_flashMessenger
                        ->addMessage('Datei ' . $deleteFilename . ' ist keine gültige Datei.', 'error');
            }
        } else {
            $this->_flashMessenger
                    ->addMessage('Formulardaten ungültig', 'error');
        }
        $this->redirect(
            $this->view->url(
                array(
                    'action' => 'index',
                    'kid' => $kid
                )
            ),
            array('prependBase' => false)
        );
    }

    /**
     * Shows contents of the popup where users can select media files or uplad new ones
     */
    public function chooseAction()
    {
        $this->_helper->layout->setLayout('popup');
        $elemid = $this->getRequest()->getParam('elemid', 0);
        $formid = $this->getRequest()->getParam('formid', 0);
        $uploadScenario = $this->getRequest()->getParam('upload_scenario', 0);
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultation = null;
        $directory = realpath(MEDIA_PATH);
        $dirWs = '/media';
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->find($kid)->current();
            if ($consultation) {
                $directory.= '/consultations/' . $kid;
                $dirWs.= '/consultations/' . $kid;
                if (!is_dir($directory)) {
                    mkdir($directory);
                }
            }
        } else {
            $directory.= '/misc';
            $dirWs.= '/misc';
        }
        $files = scandir($directory);
        natcasesort($files);
        $action = $this->view->url(
            array(
                'action' => 'delete',
                'kid' => $kid
            )
        );
        $i = 0;
        $aFileinfo = array();
        if (!empty($files)) {
            foreach ($files as $filename) {
                if (is_file($directory . '/' . $filename)) {
                    $i++;
                    $aFileinfo[$filename] = pathinfo($directory . '/' . $filename);
                    $aFileinfo[$filename]['size'] = ceil(filesize($directory . '/' . $filename) / 1024);
                }
            }
        }
        $downloadurl = $this->view->url(
            array(
                'action' => 'download',
                'kid' => $kid,
                'popup' => 1,
                'elemid' => $elemid
            )
        );

        $form = new Admin_Form_Media_Upload();
        $form->setAction(
            $this->view->url(
                array(
                    'action' => 'upload',
                    'kid' => $kid,
                    'popup' => 1,
                    'elemid' => $elemid,
                    'upload_scenario' => $uploadScenario,
                )
            )
        );
        $this->view->assign(
            array(
                'kid' => $kid,
                'elemid' => $elemid,
                'consultation' => $consultation,
                'downloadurl' => $downloadurl,
                'directory' => $dirWs,
                'files' => $aFileinfo,
                'form' => $form
            )
        );
    }

    public function downloadAction()
    {
        $file = $this->getRequest()->getParam('file', 0);
        $filename = $this->getRequest()->getParam('filename', 0);
        $elemid = $this->getRequest()->getParam('elemid', 0);
        $kid = $this->getRequest()->getParam('kid', 0);

        if ($kid) {
            $uploadDir = realpath(MEDIA_PATH . '/consultations/' . $kid);
        } else {
            $uploadDir = realpath(MEDIA_PATH . '/misc');
        }
        $file = $uploadDir . '/' . $filename;

        if (is_file($file)) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-length: " . filesize($file));
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Description: File Transfer");
            ob_clean();
            flush();
            readfile($file);
        } else {
            $this->_flashMessenger->addMessage('File does not exist.', 'error');
            $this->redirect(
                $this->view->url(
                    array(
                        'action' => 'choose',
                        'kid' => $kid,
                        'elemid' => $elemid
                    )
                ),
                array('prependBase' => false)
            );
        }
    }

}
