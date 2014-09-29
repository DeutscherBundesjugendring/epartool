<?php

use Behat\Transliterator\Transliterator;

class Service_Media
{
    const MEDIA_DIR_FOLDERS = 'folders';
    const MEDIA_DIR_CONSULTATIONS = 'consultations';

    /**
     * Maps file extensions to icon file names
     * @see application/configs/config.ini
     * @var array
     */
    private $iconMap = [
        'crystal_clear_mimetype_txt' => ['doc', 'docx', 'odt', 'txt'],
        'crystal_clear_mimetype_spreadsheet' => ['xls', 'xlsx', 'odc', 'ods'],
        'crystal_clear_mimetype_video' => ['avi', 'mkv', 'mp4', 'm4v'],
        'crystal_clear_app_xmms' => ['mp3'],
        'crystal_clear_app_display' => ['ppt', 'pptx', 'odp'],
        'cystal_clear_mimetype_pdf' => ['pdf'],
        'crystal_clear_filesystem_folder_tar' => ['zip'],
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
        $dirname = $this->getdirname($kid, $folder);
        $files = [
            [
                'basename' => $filename,
                'dirname' => $dirname,
                'kid' => $kid,
                'folder' => $folder,
            ]
        ];
        $files = $this->loadFileDetails($files);

        return reset($files);
    }

    /**
     * Returns array of file arrays that are in the same dir
     * @param  integer $kid         The consultation identifier. Mandatory if no $folder is set.
     * @param  string  $folder      The folder name. Mandatory if no $kid is set.
     * @param  boolean $acceptAll   Indicates if the inablilty to resolve kidFolder path should return all images or thorow error
     * @return array                An array of file info arrays. Each holds output of pathinfo() and the following keys
     *                              - size         The filesize
     *                              - kid          The containing consultation id if belonging to consultation
     *                              - folder       The containing folder name if held in folder
     *                              - dirUrl       The url by which the containing dir is accesible
     *                              - icon         Indicates which icon to use as thumb if file is not an image
     */
    public function getByDir($kid = null, $folder = null, $acceptAll = null)
    {
        try {
            $dirname = $this->getdirname($kid, $folder);
        } catch (Dbjr_File_Exception $e) {
            if ($acceptAll) {
                $dirname = MEDIA_PATH;
            } else {
                throw $e;
            }
        }
        $files = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname)) as $file) {
            $filedirname = $file->getPath();
            $dirArr = (explode('/', $filedirname));
            $files[] = [
                'basename' => $file->getFilename(),
                'dirname' => $filedirname,
                'kid' => $dirArr[count($dirArr) - 2] === self::MEDIA_DIR_CONSULTATIONS ? end($dirArr) : null,
                'folder' => $dirArr[count($dirArr) - 2] === self::MEDIA_DIR_FOLDERS ? end($dirArr) : null,
            ];
        }

        return $this->loadFileDetails($files);
    }

    /**
     * Filters and loads additional info into elements of an array of file info arrays
     * @param  array $inFiles The input array of file info arrays
     * @return array          The output array of file info arrays
     */
    public function loadFileDetails($inFiles)
    {
        $files = [];
        if (!empty($inFiles)) {
            for ($i = 0; $i < count($inFiles); $i++) {
                $filePath = $inFiles[$i]['dirname'] . '/' . $inFiles[$i]['basename'];
                if (is_file($filePath) && substr($inFiles[$i]['basename'], 0, 1) !== '.') {
                    $files[$i] = pathinfo($filePath);
                    $files[$i]['size'] = ceil(filesize($filePath) / 1024);
                    $files[$i]['kid'] = isset($inFiles[$i]['kid']) ? $inFiles[$i]['kid'] : null;
                    $files[$i]['folder'] = isset($inFiles[$i]['folder']) ? $inFiles[$i]['folder'] : null;
                    // Holds the name of the reference directory.
                    // It can be either a particular consultation direcotry or the root media directory
                    $files[$i]['dirRefDirUrl'] = ($files[$i]['kid'] ? self::MEDIA_DIR_CONSULTATIONS . '/' . $files[$i]['kid'] : '');
                    // Holds the filename and possibly directory to finish the path based on reference directory.
                    // Typically this is what is saved in db as reference to this image.
                    $files[$i]['dirRefFilename'] = ($files[$i]['folder'] ? self::MEDIA_DIR_FOLDERS . '/' . $files[$i]['folder'] . '/' : '') . $files[$i]['basename'];

                    if (!@getimagesize($filePath)) {
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
     * @return string|array                   The saved filename or an array of error messages on validation fail
     */
    public function upload($filename, $kid = null, $folder = null)
    {
        $filename = $this->sanitizeFilename($filename);
        $uploadDir = $this->getdirname($kid, $folder);

        if (file_exists($uploadDir . '/' . $filename)) {
            throw new Dbjr_File_Exception('File exists.');
        }

        $upload = new Zend_File_Transfer_Adapter_Http();
        $uploadRes = $upload
            ->addFilter(
                'Rename',
                ['target' => $uploadDir . '/' . $filename]
            )
            ->addValidator('Extension', false, Zend_Registry::get('systemconfig')->media->filetype->extensions)
            ->receive();

        return $uploadRes ? $filename : $upload->getMessages();
    }

    /**
     * Santizes filename to make it pass the following regexp [-a-z0-9]* and if needed trimming it
     * @param  string $filename The original filename
     * @return string           The sanitized filename
     */
    public function sanitizeFilename($filename)
    {
        if (Transliterator::validUtf8($filename)) {
            $filename = Transliterator::utf8ToAscii($filename);
        }
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $basename = substr($basename, 0, Zend_Registry::get('systemconfig')->media->filename->maxLength);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = Transliterator::urlize($basename) . '.' . $extension;

        return $filename;
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
            ->setDirPath($this->getdirname($kid, $folder))
            ->delete();

        return (bool) $res;
    }

    /**
     * Deletes a dir
     * @param  integer $kid      The consultation identifier. Mandatory if no $folder is set.
     * @param  string  $folder   The folder name. Mandatory if no $kid is set.
     * @param  boolean $force    Deletes all files in folder if present
     * @return boolean           Indicates success
     */
    public function deleteDir($kid = null, $folder = null, $force = null)
    {
        $files = $this->getByDir($kid, $folder);
        if ($force && $files) {
            foreach ($files as $file) {
                $this->delete($file['basename'], $kid, $folder);
            }
        }

        $res = (new Dbjr_File_Folder())
            ->setDirPath($this->getdirname($kid, $folder))
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
     * Renaming or moving consultation files is not supported.
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
        $dirname = MEDIA_PATH . '/' . ($kid ? self::MEDIA_DIR_CONSULTATIONS : self::MEDIA_DIR_FOLDERS) . '/' . ($kid ? $kid : $folder);

        return mkdir($dirname, 0700);
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
    private function getdirname($kid, $folder)
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
