<?php

class Service_Media
{
    const MEDIA_DIR_FOLDERS = 'folders';
    const MEDIA_DIR_CONSULTATIONS = 'consultations';

    /**
     * Maps file extensions to icon file names
     * @var array
     */
    private $iconMap = [
        'crystal_clear_mimetype_txt' => ["doc", "docx", "dot", "dotx", "odt", "ott", "rtf", "txt"],
        'crystal_clear_mimetype_spreadsheet' => ["xls", "xlsx", "xlst", "ods", "ots", "csv"],
        'crystal_clear_mimetype_video' => ["avi", "divx", "flv", "mkv", "mov", "mpg", "mp4", "mp4v", "m4v", "ogv", "webm", "wmv", "3gp", "3g2"],
        'crystal_clear_app_xmms' => ["aac", "au", "flac", "mid", "mka", "mp3", "m4a", "m4b", "ogg", "snd", "wav", "wma"],
        'crystal_clear_app_display' => ["ppt", "pptx", "odp", "otp"],
        'cystal_clear_mimetype_pdf' => ["pdf", "epub", "mobi"],
        'crystal_clear_filesystem_folder_tar' => ["zip", "rar", "tar", "7z"],
    ];

    /**
     * Returns one file array
     * @param  string  $filename Indicates the filename
     * @param  integer $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string  $folder   The folder name. Mandatory if no $kid is set.
     * @return array             The file info array holding output of pathinfo() and the following keys
     *                           - size         The filesize
     *                           - kid          The containing consultation id if belonging to consultation
     *                           - folder       The containing folder name if held in folder
     *                           - dirUrl       The url by which the containing dir is accesible
     *                           - icon         Indicates which icon to use as thumb if file is not an image
     */
    public function getOne($filename, $kid = null, $folder = null)
    {
        $dirPath = $this->getDirPath($kid, $folder);
        $files = [
            [
                'filename' => $filename,
                'dirPath' => $dirPath,
                'kid' => $kid,
                'folder' => $folder,
                'dirUrl' => (new Zend_View())->baseUrl() . '/media/' . substr($dirPath, strlen(MEDIA_PATH)),
            ]
        ];
        $files = $this->loadFileDetails($files);

        return reset($files);
    }

    /**
     * Returns array of file arrays that are in the same dir
     * @param  integer $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string  $folder   The folder name. Mandatory if no $kid is set.
     * @return array             An array of file info arrays. Each holds output of pathinfo() and the following keys
     *                           - size         The filesize
     *                           - kid          The containing consultation id if belonging to consultation
     *                           - folder       The containing folder name if held in folder
     *                           - dirUrl       The url by which the containing dir is accesible
     *                           - icon         Indicates which icon to use as thumb if file is not an image
     */
    public function getByDir($kid = null, $folder = null)
    {
        try {
            $dirPath = $this->getDirPath($kid, $folder);
        } catch(Dbjr_File_Exception $e) {
            $dirPath = MEDIA_PATH;
        }
        $files = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath)) as $file) {
            $fileDirPath = $file->getPath();
            $dirArr = (explode('/', $fileDirPath));
            $files[] = [
                'filename' => $file->getFilename(),
                'dirPath' => $fileDirPath,
                'kid' => $dirArr[count($dirArr) - 2] === self::MEDIA_DIR_CONSULTATIONS ? end($dirArr) : null,
                'folder' => $dirArr[count($dirArr) - 2] === self::MEDIA_DIR_FOLDERS ? end($dirArr) : null,
                'dirUrl' => (new Zend_View())->baseUrl() . '/media/' . substr($fileDirPath, strlen(MEDIA_PATH)),
            ];
        }

        return $this->loadFileDetails($files);
    }

    /**
     * Filters and loads additional info into elements of an array of file info arrays
     * @param  array $inFiles The input array of file info arrays
     * @return array          The output array of file info arrays
     */
    private function loadFileDetails($inFiles)
    {
        $files = [];
        if (!empty($inFiles)) {
            for ($i = 0; $i < count($inFiles); $i++) {
                $filePath = $inFiles[$i]['dirPath'] . '/' . $inFiles[$i]['filename'];
                if (is_file($filePath) && substr($inFiles[$i]['filename'], 0, 1) !== '.') {
                    $files[$i] = pathinfo($filePath);
                    $files[$i]['size'] = ceil(filesize($filePath) / 1024);
                    $files[$i]['kid'] = $inFiles[$i]['kid'];
                    $files[$i]['folder'] = $inFiles[$i]['folder'];
                    $files[$i]['dirUrl'] = $inFiles[$i]['dirUrl'];

                    if (!getimagesize($filePath)) {
                        $files[$i]['icon'] = $this->getIconName($files[$i]['extension']);
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Handles a file upload
     * @param  string               $filename The name of the upload file
     * @param  integer              $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string               $folder   The folder name. Mandatory if no $kid is set.
     * @throws Dbjr_File_Exception            Throws exception if the file already exists
     * @return boolean                        Indicates success
     */
    public function upload($filename, $kid = null, $folder = null)
    {
        $uploadDir = $this->getDirPath($kid, $folder);
        if (file_exists($uploadDir . '/' . $filename)) {
            throw new Dbjr_File_Exception('File exists.');
        }
        $uploadRes = (new Zend_File_Transfer_Adapter_Http())
            ->addFilter(
                'Rename',
                ['target' => $uploadDir . '/' . $filename]
            )
            ->receive();

        return (bool) $uploadRes;
    }

    /**
     * Deletes a file
     * @param  string  $filename The name of the upload file
     * @param  integer $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string  $folder   The folder name. Mandatory if no $kid is set.
     * @return boolean           Indicates success
     */
    public function delete($filename, $kid = null, $folder = null)
    {
        $res = (new Dbjr_File())
            ->setFilename($filename)
            ->setDirPath($this->getDirPath($kid, $folder))
            ->delete();

        return (bool) $res;
    }

    /**
     * Deletes a dir
     * @param  integer $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string  $folder   The folder name. Mandatory if no $kid is set.
     * @return boolean           Indicates success
     */
    public function deleteDir($kid = null, $folder = null)
    {
        $files = $this->getByDir($kid, $folder);
        $res = (new Dbjr_File_Folder())
            ->setDirPath($this->getDirPath($kid, $folder))
            ->delete();

        return (bool) $res;
    }

    /**
     * Renames a folder
     * @param  integer $oldFolderName   The old folder name
     * @param  string  $newFolderName   The new folder name
     * @return boolean                  Indicates success
     */
    public function renameFolder($oldFolderName, $newFolderName)
    {
        return rename(
            MEDIA_PATH . '/' . self::MEDIA_DIR_FOLDERS . '/' . $oldFolderName,
            MEDIA_PATH . '/' . self::MEDIA_DIR_FOLDERS . '/' . $newFolderName
        );
    }

    /**
     * Renames or moves a file in folders
     * @param  integer $oldName      The old folder name
     * @param  string  $newName      The new folder name
     * @param  string  $folderName   The name of the folder where the file is
     * @param  string  $folderName   The name of the folder where the file is to be
     * @return boolean               Indicates success
     */
    public function renameFile($oldName, $newName, $oldFolderName, $newFolderName)
    {
        return rename(
            MEDIA_PATH . '/' . self::MEDIA_DIR_FOLDERS . '/' . $oldFolderName . '/' . $oldName,
            MEDIA_PATH . '/' . self::MEDIA_DIR_FOLDERS . '/' . $newFolderName . '/' . $newName
        );
    }

    /**
     * Creates a dir
     * @param  integer          $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string           $folder   The folder name. Mandatory if no $kid is set.
     * @throws Dbjr_Exception             Throws exception if nor $kid nor $folder are provided
     * @return boolean                    Indicates success
     */
    public function createDir($kid = null, $folder = null)
    {
        if (!$kid && !$folder) {
            throw new Dbjr_Exception('Either Consultation Id or Folder name msut be given.');
        }
        $dirPath = MEDIA_PATH . '/' . ($kid ? self::MEDIA_DIR_CONSULTATIONS : self::MEDIA_DIR_FOLDERS) . '/' . ($kid ? $kid : $folder);

        return mkdir($dirPath, 0700);
    }

    /**
     * Returns the subdirs held in the indicated dir
     * @param  string $dirName The name of the dir @see self::MEDIA_DIR_*
     * @return array           A numeric array holding the subdir names
     */
    public function getDirs($dirName)
    {
        return (new Dbjr_File_Folder())
            ->setDirPath(MEDIA_PATH . '/' . $dirName)
            ->getSubdirNames();
    }

    /**
     * Returns icon file name based on the file extension
     * @param  string $extension The extension of the file
     * @return string            The icon filename without extension
     */
    private function getIconName($extension)
    {
        foreach ($this->iconMap as $icon => $exts) {
            if (in_array(strtolower($extension), $exts)) {
                return $icon;
            }
        }
    }

    /**
     * Returns a path to the dir
     * @param  integer              $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string               $folder   The folder name. Mandatory if no $kid is set.
     * @throws Dbjr_File_Exception            Throws exception if the path does not exist
     * @return string                         The path to the relevant dir
     */
    private function getDirPath($kid, $folder)
    {
        if ($kid && file_exists(MEDIA_PATH . '/' . self::MEDIA_DIR_CONSULTATIONS . '/' . $kid)) {
            return MEDIA_PATH . '/' . self::MEDIA_DIR_CONSULTATIONS . '/' . $kid;
        } elseif ($folder && file_exists(MEDIA_PATH . '/' . self::MEDIA_DIR_FOLDERS . '/' . $folder)) {
            return MEDIA_PATH . '/' . self::MEDIA_DIR_FOLDERS . '/' . $folder;
        } else {
            throw new Dbjr_File_Exception('The path can not be obtained.');
        }
    }
}
