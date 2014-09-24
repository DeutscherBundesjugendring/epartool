<?php

use Gregwar\Image\Image;

class Application_View_Helper_MediaPresenter extends Zend_View_Helper_Abstract
{
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
        if (empty($file['icon'])) {

            $imagePath = Image::open($file['dirname'] . '/' . $file['basename'])
                ->zoomCrop($contextConf->width, $contextConf->height)
                ->guess();
        } else {
            $icon = Image::open(dirname(__FILE__) . '/../../../www/images/' . $file['icon'] . '.png');
            $iconWidth = $icon->width();
            $iconHeight = $icon->height();
            $imagePath = IMAGE::create($contextConf->width, $contextConf->height)
                ->fill('white')
                ->merge(
                    $icon,
                    ($contextConf->width - $iconWidth) / 2,
                    ($contextConf->height - $iconHeight) / 2
                )
                ->guess();
        }

        return $this->view->baseUrl() . '/' . $imagePath;
    }
}
