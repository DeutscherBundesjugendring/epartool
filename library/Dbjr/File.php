<?php

class Dbjr_File
{
    /**
     * The filename
     * @var string
     */
    protected $_filename;

    /**
     * The path to the directory where the file is located
     * @var string
     */
    protected $_dirPath;

    /**
     * Deletes the file
     * @return boolean          Identifier if the file got deleted
     * @throws Dbjr_Exception   Throws an exception if the file could not be deleted
     */
    public function delete()
    {
        $filePath = $this->getFilePath();
        try {
            unlink($filePath);
            return true;
        }
        catch (Exception $e) {
            throw new Dbjr_Exception('File could not be deleted: ' . $filePath);
        }
    }

    /**
     * Return the canonicalized path to the file
     * @return string           The path to the file
     * @throws Dbjr_Exception   Throws an exception if the file does not exist
     */
    public function getFilePath()
    {
        $filePath = realpath($this->_dirPath . '/' . $this->_filename);
        if($filePath !== false) {
            return $filePath;
        } else {
            throw new Dbjr_Exception('File set for deletion is not accessible by the application: ' . $filePath);
        }

    }
}
