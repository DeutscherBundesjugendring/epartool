<?php

namespace Util;

class FileSystem
{
    /** @var string */
    private $rootDir;

    public function __construct(string $rootDir) {
        $this->rootDir = $rootDir;
    }

    public function getNonWritableFolders(array $folders): array
    {
        return array_filter($folders, function (string $folder) {
            return !is_writable($this->rootDir . '/' . $folder);
        });
    }

    public function createFolders(array $folders): bool
    {
        foreach ($folders as $folder) {
            $path = $this->rootDir . '/' . $folder;
            if (file_exists($path)) {
                if (!is_writable($path) && !chmod($path, 0755)) {
                    return false;
                }
            } elseif (!mkdir($path)) {
                return false;
            }
        }

        return true;
    }

    public function validateWritable(array $folders): bool
    {
        return count($this->getNonWritableFolders($folders)) === 0;
    }
}
