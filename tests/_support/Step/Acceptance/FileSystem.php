<?php

namespace Step\Acceptance;

use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

require_once __DIR__ . '/../../../../application/directories.php';

class FileSystem extends \AcceptanceTester
{
    const MEDIA_DIR = '/www/media';
    const CONSULTATIONS_DIR = 'consultations';
    const FOLDERS_DIR = 'folders';

    public function backupMedia()
    {
        if (file_exists(MEDIA_PATH . '.backup')) {
            $this->removeDirectory(MEDIA_PATH);
        } else {
            rename(MEDIA_PATH, MEDIA_PATH . '.backup');
        }
        $this->prepareMediaDirectory();
    }

    public function restoreMedia()
    {
        $this->removeDirectory(MEDIA_PATH);
        rename(MEDIA_PATH . '.backup', MEDIA_PATH);
    }

    private function prepareMediaDirectory()
    {
        mkdir(MEDIA_PATH);
        mkdir(MEDIA_PATH . DIRECTORY_SEPARATOR . self::CONSULTATIONS_DIR);
        mkdir(MEDIA_PATH . DIRECTORY_SEPARATOR . self::FOLDERS_DIR);
    }

    private function removeDirectory($dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it,
            \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        $this->comment($dir);
        rmdir($dir);
    }
}
