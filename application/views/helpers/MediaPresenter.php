<?php

use Gregwar\Image\Image;

class Application_View_Helper_MediaPresenter extends Zend_View_Helper_Abstract
{

    /**
     * Holds the dirpath of the media cache dir relative to the base url
     */
    const CACHE_DIR = 'image_cache';

    /**
     * Holds the dirpath of the media cache dir relative to the APPLICATION_PATH
     * PHP cant concatenate a constant to  a string in constant assignment
     */
    const CACHE_ACTUAL_DIR = '/../www/image_cache';

    /**
     * The pathof the fallback image to be used if the real image is not available
     * Relative to APPLICATION_PATH
     */
    const FALLBACK_IMAGE_PATH = "/../www/images/admin/file-type-icons/icon_file_default.png";

    /**
     * Returns url to the image representation of the given media
     * Typically it is a resize image for images and an icon for other file types
     * @param  string $file         The file array @see Service_Media::getOne() and Service_Media::getByDir()
     * @param  string $context      The context where the image is to be presented
     * @return string               The url to the image representation of the media file
     */
    public function mediaPresenter($file, $context)
    {
        ini_set('memory_limit', '256M');
        $contextConf = Zend_Registry::get('systemconfig')
            ->media
            ->presentationContext
            ->$context;

        if (!is_array($file)) {
            $file = pathinfo($file);
            $file = reset((new Service_Media())->loadFileDetails([$file]));
        }

        if (empty($file['icon'])) {
            $path = $file['dirname'] . '/' . $file['basename'];
            $image = Image::open(is_file($path) ? $path :  APPLICATION_PATH . self::FALLBACK_IMAGE_PATH);
            if ($contextConf->method === 'zoomCropScale') {
                $image->zoomCrop($contextConf->width, $contextConf->height);
            } elseif ($contextConf->method === 'zoomCropFill') {
                try {
                    $newImage = Image::create($contextConf->width, $contextConf->height)
                        ->fill('#eeeeee')
                        ->merge(
                            $image,
                            ($contextConf->width - $image->width()) / 2,
                            ($contextConf->height - $image->height()) / 2
                        );
                    $image = $newImage;
                } catch (UnexpectedValueException $e) {
                    // The file probably doesnt exist, we continue and let the fallback image be used
                }
            } elseif ($contextConf->method === 'scaleResize') {
                $image = Image::open(is_file($path) ? $path :  APPLICATION_PATH . self::FALLBACK_IMAGE_PATH);
                $image->scaleResize($contextConf->width, $contextConf->height);
            } elseif ($contextConf->method === 'cropResize') {
                $image = Image::open(is_file($path) ? $path :  APPLICATION_PATH . self::FALLBACK_IMAGE_PATH);
                $image->cropResize($contextConf->width, $contextConf->height);
            }

            $imagePath = $image
                ->setCacheDir(self::CACHE_DIR)
                ->setActualCacheDir(APPLICATION_PATH . self::CACHE_ACTUAL_DIR)
                ->guess();
        } else {
            $icon = Image::open(APPLICATION_PATH . '/../www/images/admin/file-type-icons/' . $file['icon'] . '.png');
            $imagePath = Image::create($contextConf->width, $contextConf->height)
                ->fill('#ffffff')
                ->merge(
                    $icon,
                    ($contextConf->width - $icon->width()) / 2,
                    ($contextConf->height - $icon->height()) / 2
                )
                ->setCacheDir(self::CACHE_DIR)
                ->setActualCacheDir(RUNTIME_PATH . self::CACHE_ACTUAL_DIR)
                ->guess();
        }

        return (new Zend_View())->baseUrl() . '/' . $imagePath;
    }
}
