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
    public function openStreetStaticMap($latitude, $longitude, $width, $height, $zoom, $placeMarker = false)
    {
        $osmConfig = Zend_Registry::get('systemconfig')->osm;

        return sprintf(
            '<img src="%s?center=%f,%f&zoom=%d&size=%dx%d&maptype=mapnik%s" alt="GPS" />',
            $osmConfig->static_map_url,
            $latitude,
            $longitude,
            $zoom,
            $width,
            $height,
            ($placeMarker ? sprintf('&markers=%f,%f,ltblu-pushpin', $latitude,$longitude) : '')
        );
    }
}
