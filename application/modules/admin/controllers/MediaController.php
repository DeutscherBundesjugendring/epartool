<?php

class Admin_MediaController extends Zend_Controller_Action
{
    /**
     * FlashMessenger
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger = null;

    /**
     * If relevant holds the kid context
     * @var integer
     */
    private $_kid;

    /**
     * If relevant holds the folder context
     * @var string
     */
    private $_folder;

    /**
     * If relevant holds the filename context
     * @var string
     */
    private $_filename;

    /**
     * Identifies the target element if the media are displayed in popup context
     * @var string
     */
    private $_targetElId;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();

        // This controller can output either to main window or to a popup window depending on context
        if ($this->getRequest()->getParam('isPopup', null)) {
            $this->_helper->layout->setLayout('backend-popup');
        }

        $this->_kid = $this->getRequest()->getParam('kid', null);
        $this->view->kid = $this->_kid;
        $this->_folder = $this->getRequest()->getParam('folder', null);
        $this->view->folder = $this->_folder;
        $this->_filename = $this->getRequest()->getParam('filename', null);
        $this->_targetElId = $this->getRequest()->getParam('targetElId', null);

        try {
            $invalidUrl = false;
            if ($this->_filename && !(new Service_Media())->getOne($this->_filename, $this->_kid, $this->_folder)) {
                $invalidUrl = true;
            }
        } catch (Dbjr_File_Exception $e) {
            $invalidUrl = true;
        }
        if ($invalidUrl) {
            $this->redirect(
                $this->view->url(['module' => 'admin', 'controller' => 'media', 'action' => 'index'], null, true),
                ['prependBase' => false]
            );
        }

        // If targetElId is set, then the action is to be used as image selector in popup context
        $this->view->targetElId = $this->_targetElId;
        if ($this->view->targetElId) {
            $this->_helper->layout->setLayout('backend-popup');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/admin_mediaPopup.js');
            $this->view->lockDir = (bool) $this->getRequest()->getParam('lockDir', null);
        }
    }

    /**
     * Shows media dashboard
     */
    public function indexAction()
    {
        $files = (new Service_Media())->getByDir($this->_kid, $this->_folder, !$this->_kid);
        foreach ($files as $i => &$file) {
            $deleteForm = (new Admin_Form_Media_Delete());
            $deleteForm
                ->setAction($this->view->url(['action' => 'delete-file']))
                ->setAttrib('name', 'delete_' . $i)
                ->setAttrib('id', 'delete_' . $i)
                ->addCsrfHash('csrf_token_mediadelete_' . $i)
                ->populate(
                    [
                        'file' => $file['basename'],
                        'kid' => $file['kid'],
                        'folder' => $file['folder'],
                        'form_num' => $i
                    ]
                );
            $file['deleteForm'] = $deleteForm;
        }

        if ($this->_kid) {
            $consModel = new Model_Consultations();
            $consultation = $consModel->fetchRow(
                $consModel
                    ->select()
                    ->from($consModel->info(Model_Consultations::NAME), ['titl'])
                    ->where('kid=?', $this->_kid)
            );
            $this->view->title = $consultation->titl;
        } elseif ($this->_folder) {
            $this->view->title = $this->view->translate('Folder') . ': ' . $this->_folder;
        } else {
            $this->view->title = $this->view->translate('All Media');
        }

        $this->view->files = $files;
        $this->view->CKEditorFuncNum = $this->getRequest()->getParam('CKEditorFuncNum', 0);
        // If the consultation id is set then the path to the image folder is assumed and we only deal with filename relative to
        // the consultation media folder. Otherwise we deal with path relative to the media/folders folder
    }

    public function editFolderAction()
    {
        $form = new Admin_Form_Media_FolderDetail();
        $form->getElement('submit')->setLabel('Rename');

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $newName = $postData['name'];
            $oldName = $postData['oldName'];
            if ($oldName === $newName) {
                $form->getElement('name')->removeValidator('File_NotExists');
            }
            if ($form->isValid($postData)) {
                if ((new Service_Media())->renameFolder($oldName, $newName)) {
                    $this->_flashMessenger->addMessage(sprintf('The folder %s has been renamed to %s.', $oldName, $newName), 'success');
                    $this->redirect(
                        $this->view->url(['module' => 'admin', 'controller' => 'media', 'action' => 'folders'], null, true),
                        ['prependBase' => false]
                    );
                } else {
                    $this->_flashMessenger->addMessage(sprintf('The folder %s could not be renamed to %s.', $oldName, $newName), 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('The folder could not be renamed.', 'error');
            }
        }

        $form->populate(
            [
                'name' => isset($postData) ? $postData['name'] : $this->_folder,
                'oldName' => $this->_folder,
            ]
        );


        $this->view->form = $form;
        $this->view->pageTitle = 'Rename Folder';
        $this->render('folder-detail');
    }

    public function editFileAction()
    {
        $form = new Admin_Form_Media_FileDetail();
        $form->getElement('submit')->setLabel('Rename');

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $newName = $postData['name'];
            $newDir = $postData['folder'];
            $oldName = $postData['oldName'];

            if ($this->_folder === $newDir && $oldName === $newName) {
                $form->getElement('name')->removeValidator('File_NotExists');
            }
            $form->setDirectory($newDir);
            if ($form->isValid($postData)) {
                if ((new Service_Media())->renameFile($oldName, $newName, $this->_folder, $newDir)) {
                    if ($this->_folder === $newDir) {
                        $this->_flashMessenger->addMessage(
                            sprintf('The file %s has been renamed to %s.', $oldName, $newName),
                            'success'
                        );
                    } else {
                        $this->_flashMessenger->addMessage(
                            sprintf('The file %s/%s has been moved to %s/%s.', $this->_folder, $oldName, $newDir, $newName),
                            'success'
                        );
                    }
                    $this->redirect(
                        $this->view->url(['module' => 'admin', 'controller' => 'media', 'action' => 'index', 'folder' => $newDir], null, true) . '/',
                        ['prependBase' => false]
                    );
                } else {
                    $this->_flashMessenger->addMessage(
                        sprintf('The file %s could not be renamed to %s.', $oldName, $newName),
                        'error'
                    );
                }
            } else {
                $this->_flashMessenger->addMessage('The file could not be renamed.', 'error');
            }
        }

        $form->populate(
            [
                'name' => isset($postData) ? $postData['name'] : $this->_filename,
                'oldName' => $this->_filename,
                'folder' => isset($postData) ? $postData['folder'] : $this->_folder,
            ]
        );

        $this->view->form = $form;
    }

    /**
     * Lists all folders
     */
    public function foldersAction()
    {
        $form = new Admin_Form_ListControl();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                (new Service_Media())->deleteDir(null, $this->getRequest()->getPost('delete'));
            }
        }

        $foldersRaw = (new Service_Media())->getDirs(Service_Media::MEDIA_DIR_FOLDERS);
        $folders = [];
        foreach ($foldersRaw as $folder) {
            $folders[] = [
                'isEmpty' => !(new \FilesystemIterator(MEDIA_PATH . '/' . Service_Media::MEDIA_DIR_FOLDERS . '/' . $folder))->valid(),
                'name' => $folder,
            ];
        }

        $this->view->folderDirs = $folders;
        $this->view->form = $form;
    }

    /**
     * Lists all consultation directories
     */
    public function consultationsAction()
    {
        $consModel = new Model_Consultations();
        $consRaw = $consModel->fetchAll(
            $consModel->select()->from($consModel->info(Model_Consultations::NAME), ['kid', 'titl'])
        );
        $consultations = [];
        foreach ($consRaw as $cons) {
            $consultations[$cons->kid] = $cons->titl;
        }

        $this->view->consultationDirs = (new Service_Media())->getDirs(Service_Media::MEDIA_DIR_CONSULTATIONS);
        $this->view->consultations = $consultations;
    }

    /**
     * Prompts user to create a new folder
     */
    public function createFolderAction()
    {
        $form = new Admin_Form_Media_FolderDetail();
        $form->getElement('submit')->setLabel('Create');
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $folderName = $form->getValue('name');
                if ((new Service_Media())->createDir(null, $folderName)) {
                    $this->_flashMessenger->addMessage(sprintf('The folder %s has been created.', $folderName), 'success');
                    $this->redirect(
                        $this->view->url(['module' => 'admin', 'controller' => 'media', 'action' => 'folders'], null, true),
                        ['prependBase' => false]
                    );
                } else {
                    $this->_flashMessenger->addMessage(sprintf('The folder %s could not be created.', $folderName), 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('The folder could not be created.', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->pageTitle = 'New Folder';
        $this->render('folder-detail');
    }

    /**
     * Handles Upload Action, redirects to index view
     * @throws  Dbjr_Excetion  If the folder prefix is invalid
     */
    public function uploadAction()
    {
        $form = new Admin_Form_Media_Upload();
        if ($this->_kid || $this->_folder) {
            $form->getElement('directory')->setValue(
                $this->_kid
                ? Admin_Form_Media_Upload::DIR_TYPE_PREFIX_CONSULTATIONS . $this->_kid
                : Admin_Form_Media_Upload::DIR_TYPE_PREFIX_FOLDERS . $this->_folder
            );
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $directory = $this->getRequest()->getParam('directory', null);
                if (strpos($directory, Admin_Form_Media_Upload::DIR_TYPE_PREFIX_CONSULTATIONS) === 0) {
                    $this->_kid = substr_replace($directory, '', 0, strlen(Admin_Form_Media_Upload::DIR_TYPE_PREFIX_CONSULTATIONS));
                } elseif (strpos($directory, Admin_Form_Media_Upload::DIR_TYPE_PREFIX_FOLDERS) === 0) {
                    $this->_folder = substr_replace($directory, '', 0, strlen(Admin_Form_Media_Upload::DIR_TYPE_PREFIX_FOLDERS));
                } else {
                    throw new Dbjr_Exception('Invalid directory prefix.');
                }

                $filename = Dbjr_File::pathinfoUtf8($form->file->getFileName(), PATHINFO_BASENAME);
                try {
                    $uploadRes = (new Service_Media())->upload($filename, $this->_kid, $this->_folder);
                    // $uploadRes is either the new filename or an array of error messages
                    if (!is_array($uploadRes)) {
                        $this->_flashMessenger->addMessage(
                            sprintf($this->view->translate('The file %s has been successfully uploaded.'), $uploadRes),
                            'success'
                        );

                        $redirectArr = ['module' => 'admin', 'controller' => 'media', 'action' => 'index'];
                        if ($this->_targetElId) {
                            $redirectArr['targetElId'] = $this->_targetElId;
                        }
                        $lockDir = $this->getRequest()->getParam('lockDir', null);
                        if ($lockDir) {
                            $redirectArr['lockDir'] = $lockDir;
                        }
                        if ($this->_kid) {
                          $redirectArr['kid'] = $this->_kid;
                        }
                        if ($this->_folder) {
                          $redirectArr['folder'] = $this->_folder;
                        }
                        $this->redirect($this->view->url($redirectArr, null, true), ['prependBase' => false]);
                    } else {
                        $form->getElement('file')->addErrors($uploadRes);
                        $this
                            ->_flashMessenger
                            ->addMessage('File could not be uploaded.', 'error');
                    }
                } catch (Dbjr_File_Exception $e) {
                    $this->_flashMessenger->addMessage('File already exists.', 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('File could not be uploaded.', 'error');
            }
        }

        $this->view->allowedFileTypes = Zend_Registry::get('systemconfig')->media->filetype->extensions->toArray();
        $this->view->form = $form;
    }

    /**
     * Handles Delete Action, redirects to index view
     */
    public function deleteFileAction()
    {
        $formData = $this->_request->getParams();
        $form = new Admin_Form_Media_Delete();
        $form->addCsrfHash('csrf_token_mediadelete_' . $formData['form_num']);
        if ($form->isValid($formData)) {
            $filename = $form->getElement('file')->getValue();
            if ((new Service_Media())->delete($filename, $this->_kid, $this->_folder)) {
                $this
                    ->_flashMessenger
                    ->addMessage(sprintf('The file %s has been deleted.', $filename), 'success');
            } else {
                $this->_flashMessenger->addMessage('File could not be deleted.', 'error');
            }
        } else {
            $this->_flashMessenger->addMessage('File could not be deleted.', 'error');
        }

        $this->redirect(
            $this->view->url(
                [
                    'module' => 'admin',
                    'controller' => 'media',
                    'action' => 'index',
                    'kid' => $this->_kid,
                    'folder' => $this->_folder
                ],
                null,
                true
            ),
            ['prependBase' => false]
        );
    }

    /**
     * Sends a file as download
     */
    public function downloadAction()
    {
        $file = (new Service_Media())->getOne($this->_filename, $this->_kid, $this->_folder);
        $filePath = realpath($file['dirname'] . '/' . $file['basename']);

        if (is_file($filePath)) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-Disposition: attachment;filename={$file['basename']}");
            header("Content-Description: File Transfer");
            readfile($filePath);
            exit;
        } else {
            $this->_flashMessenger->addMessage('File does not exist.', 'error');
            $this->redirect(
                $this->view->url(['module' => 'admin', 'controller' => 'media', 'action' => 'index'], null, true),
                ['prependBase' => false]
            );
        }
    }

    /**
     * Sends a file to be opened by the browser
     */
    public function openAction()
    {
        $file = (new Service_Media())->getOne($this->_filename, $this->_kid, $this->_folder);
        $filePath = realpath($file['dirname'] . '/' . $file['basename']);

        if (is_file($filePath)) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-type:');
            header("Content-Transfer-Encoding: Binary");
            header("Content-Disposition: inline");
            header("Content-Description: File Transfer");
            readfile($filePath);
            exit;
        } else {
            $this->_flashMessenger->addMessage('File does not exist.', 'error');
            $this->redirect(
                $this->view->url(['module' => 'admin', 'controller' => 'media', 'action' => 'index'], null, true),
                ['prependBase' => false]
            );
        }
    }
}
