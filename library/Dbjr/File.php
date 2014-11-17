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

    /**
     * adds utf8 support to pathinfo() php function
     * pathinfo() just strips the utf8 characters
     * @param  string           $path     The input path
     * @param  int              $options  The options @see pathinfo() php function
     * @return array|string               The pathinfo array or string
     */
    public static function pathinfoUtf8($path, $options = null)
    {
        if (strpos($path, '/') === false) {
            $pathParts = pathinfo('a' . $path, $options);
        } else {
            $path = str_replace('/', '/a', $path);
            if ($options) {
                $pathParts = pathinfo($path, $options);
            } else {
                $pathParts = pathinfo($path);
            }
        }

        if ($options) {
            return substr($pathParts, 1);
        } else {
            foreach ($pathParts as $key => &$value) {
                if ($key === 'extension') {
                    continue;
                } elseif (strpos($value, '/') === false) {
                    $value = substr($value, 1);
                } else {
                    $value = str_replace('/a', '/', $value);
                }
            }
            return $pathParts;
        }
    }
}
