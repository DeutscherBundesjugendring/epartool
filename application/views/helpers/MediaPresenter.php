<?php

use Gregwar\Image\Image;

class Application_View_Helper_MediaPresenter extends Zend_View_Helper_Abstract
{

    /**
     * Holds the dirpath of the media cache dir relative to the base url
     */
    const CACHE_DIR = 'runtime/cache/media/images';

    /**
     * Holds the dirpath of the media cache dir relative to the RUNTIME_PATH
     * PHP cant concatenate a constant to  a string in constant assignment
     */
    const CACHE_ACTUAL_DIR_IN_RUNTIME = '/cache/media/images';

    /**
     * Returns url to the image representation of the given media
     * Typically it is a resize image for images and an icon for other file types
     * @param  string $file         The file array @see Service_Media::getOne() and Service_Media::getByDir()
     * @param  string $context      The context where the image is to be presented
     * @return string               The url to the image representation of the media file
     */
    public function mediaPresenter($file, $context)
    {
        $contextConf = Zend_Registry::get('systemconfig')
            ->media
            ->presentationContext
            ->$context;

        if (!is_array($file)) {
            $file = pathinfo($file);
            $file = reset((new Service_Media())->loadFileDetails([$file]));
        }

        if (empty($file['icon'])) {
            $imagePath = Image::open($file['dirname'] . '/' . $file['basename'])
                ->zoomCrop($contextConf->width, $contextConf->height)
                ->setCacheDir(self::CACHE_DIR)
                ->setActualCacheDir(RUNTIME_PATH . self::CACHE_ACTUAL_DIR_IN_RUNTIME)
                ->guess();
        } else {
            $icon = Image::open(APPLICATION_PATH . '/../www/images/' . $file['icon'] . '.png');
            $iconWidth = $icon->width();
            $iconHeight = $icon->height();
            $imagePath = IMAGE::create($contextConf->width, $contextConf->height)
                ->fill('white')
                ->merge(
                    $icon,
                    ($contextConf->width - $iconWidth) / 2,
                    ($contextConf->height - $iconHeight) / 2
                )
                ->setCacheDir(self::CACHE_DIR)
                ->setActualCacheDir(RUNTIME_PATH . self::CACHE_ACTUAL_DIR_IN_RUNTIME)
                ->guess();
        }

        return (new Zend_View())->baseUrl() . '/' . $imagePath;
    }
}
