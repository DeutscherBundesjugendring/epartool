<?php

class Admin_Form_Media_FileDetail extends Dbjr_Form_Admin
{
    /**
     * Holds the name of the target directory where the file is to be moved
     * Needed for validation if filename is unique
     * @var string
     */
    private $_directory;

    public function init()
    {
        $this
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/media']);

        $file = $this->createElement('text', 'name');
        $file
            ->setLabel('Name')
            ->setRequired(true)
            ->addValidator(
                (new Zend_Validate_File_NotExists())
                    ->addDirectory(MEDIA_PATH . '/' . Service_Media::MEDIA_DIR_FOLDERS . '/' . $this->_directory)
            )
            ->addValidator(
                'regex',
                false,
                [
                    'pattern' => '/^[-a-z0-9]+\.[a-z0-9]+$/',
                    'messages' => [Zend_Validate_Regex::NOT_MATCH => Zend_Registry::get('Zend_Translate')->translate(
                        'File name has to have an extension and can contain only characters a-z, 0-9 and -.'
                    )],
                ]
            )
            ->addValidator('stringLength', false, ['max' => Zend_Registry::get('systemconfig')->media->filename->maxLength]);
        $this->addElement($file);

        $folderDirs = (new Service_Media())->getDirs(Service_Media::MEDIA_DIR_FOLDERS);
        $folder = $this->createElement('select', 'folder');
        $folder
            ->setLabel('Folder')
            ->setMultioptions(array_combine($folderDirs, $folderDirs));
        $this->addElement($folder);


        // This is here to be used in rename scenario
        $oldFile = $this->createElement('hidden', 'oldName');
        $this->addElement($oldFile);


        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_media_file_detail', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);


        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Submit');
        $this->addElement($submit);
    }

    public function setDirectory($directory)
    {
        $this->_directory = $directory;
    }
}
