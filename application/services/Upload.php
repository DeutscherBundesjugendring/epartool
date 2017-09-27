<?php

class Service_Upload
{
    /**
     * @return string
     */
    public function getMaxUploadSize()
    {
        $maxFileSize = $this->convertDataSizeToBytes(trim(ini_get('upload_max_filesize')));
        $maxUploadSize = $this->convertDataSizeToBytes(trim(ini_get('post_max_size')));

        if ($maxUploadSize > $maxFileSize) {
            return $this->convertBytesToHumanReadable($maxFileSize);
        }

        return $this->convertBytesToHumanReadable($maxUploadSize);
    }

    /**
     * @param string $size
     * @return int
     */
    private function convertDataSizeToBytes($size)
    {
        $unit = strtolower(mb_substr($size, mb_strlen($size) - 1, 1));
        $bytes = (int) mb_substr($size, 0, mb_strlen($size) - 1);
        // 29.09.2017 jiri@visionapps.cz - missing break commands are not a mistake
        switch ($unit) {
            case 'g':
                $bytes *= 1024;
            case 'm':
                $bytes *= 1024;
            case 'k':
                $bytes *= 1024;
            default:
                break;
        }

        return $bytes;
    }

    /**
     * @param int $bytes
     * @return string
     */
    private function convertBytesToHumanReadable($bytes)
    {
        if ($bytes > 1024) {
            $bytes /= 1024;
            $bytes = floor($bytes);
            if ($bytes > 1024) {
                $bytes /= 1024;
                $bytes = floor($bytes);
                if ($bytes > 1024) {
                    $bytes /= 1024;
                    $bytes = floor($bytes);

                    return $bytes . ' GB';
                }

                return $bytes . ' MB';
            }

            return $bytes . ' KB';
        }

        return $bytes . ' B';
    }
}
