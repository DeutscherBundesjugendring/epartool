<?php

class Admin_Form_Media_FolderDetail extends Dbjr_Form_Admin
{
    public function init()
    {
        $folder = $this->createElement('text', 'name');
        $folder
            ->setLabel('Name')
            ->setRequired(true)
            ->addValidator(
                (new Zend_Validate_File_NotExists())
                    ->addDirectory(MEDIA_PATH . '/' . Service_Media::MEDIA_DIR_FOLDERS)
            );
        $this->addElement($folder);


        // This is here to be used in rename scenario
        $oldFolder = $this->createElement('hidden', 'oldName');
        $this->addElement($oldFolder);


        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_media_folder_detail', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Submit');
        $this->addElement($submit);
    }
}
