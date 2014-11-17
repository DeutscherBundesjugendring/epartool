<?php

/**
 * A class to handle all image manipulation
 * Requires the GD library to be enabled
 */
class Dbjr_File_Folder extends Dbjr_File
{
    /**
     * Deletes the folder
     * @return boolean               Identifier if the file got deleted
     * @throws Dbjr_File_Exception   Throws an exception if the file could not be deleted
     */
    public function delete()
    {
        $filePath = $this->getFilePath();
        try {
            rmdir($filePath);
            return true;
        }
        catch (Dbjr_File_Exception $e) {
            throw new Dbjr_File_Exception(sprintf('The folder marked for deletion does not exist: %s.', $filePath));
        }
    }

    /**
     * Prevents the parent::setFilename() from being called
     * Folders have no filenames
     * @param   string          $filename The filename to be set
     * @throws  Dbjr_Exception            Allways
     */
    public function setFilename($filename)
    {
        throw new Dbjr_Exception('Folder has no filename to set.');
    }

    /**
     * Returns a list of subfolders whose names dont begin with .
     * @return array A numeric array of folder names
     */
    public function getSubdirNames()
    {
        $dirs = [];
        $fileNames = scandir($this->_dirPath);
        foreach ($fileNames as $file) {
            if (is_dir($this->_dirPath . '/' . $file) && substr($file, 0, 1) !== '.') {
                $dirs[] = $file;
            }
        }
        sort($dirs);

        return $dirs;
    }
}
