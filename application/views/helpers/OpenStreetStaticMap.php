<?php

/**
 * Class Admin_View_Helper_EmbeddedVideo
 */
class Application_View_Helper_OpenStreetStaticMap extends Zend_View_Helper_Abstract
{

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param int $zoom
     * @param bool $placeMarker
     * @throws \Zend_Exception
     * @return string
     */
    public function openStreetStaticMap($latitude, $longitude, $width, $height, $zoom, $placeMarker = true)
    {
        return sprintf(
            '<img src="%s?center=%f,%f&zoom=%d&size=%dx%d%s" alt="GPS" />',
            $this->view->url([
                'module' => 'api',
                'controller' => 'open-street-map',
                'action' => 'static-map',
            ], 'osm_api'),
            $latitude,
            $longitude,
            $zoom,
            $width,
            $height,
            ($placeMarker ? sprintf('&markers=%f,%f', $latitude, $longitude) : '')
        );
    }
}
