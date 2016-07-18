<?php

namespace Util;

class FileSystem
{
    private $configPath;
    private $logPath;
    private $sessionPath;
    private $cachePath;
    private $consultationsPath;
    private $foldersPath;

    /**
     * FileSystem constructor.
     * @param string $configPath
     * @param string $logPath
     * @param string $sessionPath
     * @param string $cachePath
     * @param string $consultationsPath
     * @param string $foldersPath
     */
    public function __construct($configPath, $logPath, $sessionPath, $cachePath, $consultationsPath, $foldersPath)
    {
        $this->configPath = $configPath;
        $this->logPath = $logPath;
        $this->sessionPath = $sessionPath;
        $this->cachePath = $cachePath;
        $this->consultationsPath = $consultationsPath;
        $this->foldersPath = $foldersPath;
    }

    /**
     * @return bool
     */
    public function validateWritable()
    {
        return is_writable($this->configPath)
        && is_writable($this->logPath)
        && is_writable($this->sessionPath)
        && is_writable($this->cachePath)
        && is_writable($this->consultationsPath)
        && is_writable($this->foldersPath);
    }

    /**
     * @return bool
     */
    public function createFolders()
    {
        $folders = [
            $this->configPath,
            $this->logPath,
            $this->sessionPath,
            $this->cachePath,
            $this->consultationsPath,
            $this->foldersPath,
        ];

        foreach ($folders as $path) {
            if (file_exists($path)) {
                if (!chmod($path, 0777)) {
                    return false;
                }
            } else {
                if (!mkdir($path)) {
                    return false;
                }
            }
        }

        return true;
    }
}
