<?php

/**
 * A class to handle all image manipulation
 * Requires the GD library to be enabled
 */
class Dbjr_File_Image extends Dbjr_File
{
    /**
     * Holds the image instance
     * @var gd image resource identifier
     */
    protected $_image;

    /**
     * The width of the image
     * @var int
     */
    protected $_imageWidth;

    /**
     * The image height
     * @var int
     */
    protected $_imageHeight;

    /**
     * Image format type
     * @var the image MIME data
     */
    protected $_imageType;

    /**
     * Sets the image that is to be manipulated
     * @param string $imagePath The path to the file that is to be manipulated
     * @return $this            Fluent interface
     * @throws Dbjr_Exception   Throws an exception if the path doesn exist or does not contain an image.
     */
    public function setImage($imagePath)
    {
        if ($imagePath = realpath($imagePath)) {
            if (preg_match("/(.*)\/(.+\.(jpg|JPG|gif|GIF|png|PNG)+)$/", $imagePath, $matches)) {
               $this->_dirPath = $matches[1];
               $this->_filename = $matches[2];
            } else {
                throw new Dbjr_Exception('The supplied path does not seem to contain an image.');
            }
        } else {
            throw new Dbjr_Exception('The supplied path does not exist.');
        }

        list($this->_imageWidth, $this->_imageHeight, $this->_imageType) = getimagesize($imagePath);
        if ($this->_imageType == IMAGETYPE_GIF) {
            $this->_image = imagecreatefromgif($imagePath);
        } elseif ($this->_imageType == IMAGETYPE_PNG) {
            $this->_image = imagecreatefrompng($imagePath);
        } elseif ($this->_imageType == IMAGETYPE_JPEG) {
            $this->_image = imagecreatefromjpeg($imagePath);
        }

        return $this;
    }

    /**
     * Creates a resized copy of the uploaded image
     * @param  int $width         The required width
     * @param  int $height        The required height
     * @param  string $outPath    The path where the copy is to be saved. Optional if $filePrefix is provided.
     * @param  string $filePrefix The prefix to be prepended to the filename if no outpath path was supplied.
     *                            Optional if $outPath is provided.
     * @return $this              Fluent interface
     * @throws Dbjr_Exception     Throws an exception if no $outPath nor $filePrefix was provided
     */
    public function copyAndResize($width, $height, $outPath=null, $filePrefix=null)
    {
        if ($outPath === null && $filePrefix !== null) {

            $outPath = $this->_dirPath . '/' . $filePrefix . $this->_filename;
        } elseif ($outPath === null) {
            throw new Dbjr_Exception('Either a outPath or a file prefix must be provided.');
        }

        return $this->resizeInternal($width, $height, $outPath);
    }

    /**
     * Resize the image
     * @param  int $width  The desired width
     * @param  int $height The desired height
     * @return $this       Fluent interface
     */
    public function resize($width, $height)
    {
        $outPath = $this->getFilePath();

        return $this->resizeInternal($width, $height, $outPath);
    }

    /**
     * Resizes the image and outputs it to the specified path.
     * If an image already exists at the path, it is overwritten
     * @param  int $width      The width of the new image
     * @param  int $height     The height of the new image
     * @param  string $outPath The output path
     * @return $this           Fluent interface
     */
    protected function resizeInternal($width, $height, $outPath)
    {
        $widthRatio = $this->_imageWidth / $width;
        $heightRatio = $this->_imageHeight / $height;

        if ($heightRatio < $widthRatio) {
            $srcWidth = $heightRatio * $width;
            $srcHeight = $this->_imageHeight;
        } else {
            $srcHeight = $widthRatio * $height;
            $srcWidth = $this->_imageWidth;
        }

        $outImage = imagecreatetruecolor($width, $height);
        $this->_imageWidth = $width;
        $this->_imageHeight = $height;
        imagecopyresampled($outImage, $this->_image, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

        if ($this->_imageType == IMAGETYPE_GIF) {
            imagegif($outImage, $outPath);
        } elseif ($this->_imageType == IMAGETYPE_PNG) {
            imagepng($outImage, $outPath);
        } elseif ($this->_imageType == IMAGETYPE_JPEG) {
            imagejpeg($outImage, $outPath);
        }

        return $this;
    }
}
