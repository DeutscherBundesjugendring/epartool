<?php

class Admin_Form_Media_Upload extends Dbjr_Form_Admin
{
    const DIR_TYPE_PREFIX_CONSULTATIONS = 'cons_';
    const DIR_TYPE_PREFIX_FOLDERS = 'fold_';

    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/media']);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_mediaupload', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);


        $consultationDirs = (new Service_Media())->getDirs(Service_Media::MEDIA_DIR_CONSULTATIONS);
        $folderDirs = (new Service_Media())->getDirs(Service_Media::MEDIA_DIR_FOLDERS);
        $consModel = new Model_Consultations();
        $consRaw = $consModel->fetchAll(
            $consModel
                ->select()
                ->from($consModel->info(Model_Consultations::NAME), ['kid', 'titl'])
                ->where('kid IN (?)', $consultationDirs)
        );
        $consultationOpts = [];
        foreach ($consRaw as $cons) {
            $consultationOpts[self::DIR_TYPE_PREFIX_CONSULTATIONS . $cons->kid] = $cons->kid . ' ' . $cons->titl;
        }

        $dirOpts = [
            $translator->translate('Consultations') => $consultationOpts,
            $translator->translate('Folders') => array_combine(
                array_map(function($el) {return Admin_Form_Media_Upload::DIR_TYPE_PREFIX_FOLDERS . $el;}, $folderDirs),
                $folderDirs
            ),
        ];
        $folder = $this->createElement('select', 'directory');
        $folder
            ->setLabel('Destination')
            ->setMultioptions($dirOpts);
        $this->addElement($folder);


        $file = $this
            ->createElement('file', 'file')
            ->setRequired(true);
        $this->addElement($file);


        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Upload');
        $this->addElement($submit);

    }
}
