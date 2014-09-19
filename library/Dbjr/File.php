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
        catch (Dbjr_Exception $e) {
            throw new Dbjr_Exception(sprintf('File marked for deletion does not exist: %s.', $filePath));
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
        if ($filePath !== false) {
            return $filePath;
        } else {
            throw new Dbjr_Exception(sprintf('There is no file accessible by the application at the specified location: %s', $filePath));
        }
    }

    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    public function setDirPath($dirPath)
    {
        $this->_dirPath = $dirPath;
        return $this;
    }
}
