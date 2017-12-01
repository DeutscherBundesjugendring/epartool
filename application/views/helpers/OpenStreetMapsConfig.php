<?php

/**
 * Class Admin_View_Helper_EmbeddedVideo
 */
class Application_View_Helper_OpenStreetMapsConfig extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     * @throws Exception
     */
    public function openStreetMapsConfig()
    {
        $osmConfig = Zend_Registry::get('systemconfig')->osm;

        return '<script type="text/javascript">
            var osmConfig = {
                attribution: \'' . $osmConfig->attribution . '\',
                dataServerUrl: \'' . $osmConfig->data_server_url . '\',
                defaultLocation: {
                    latitude: \'' . $osmConfig->default_location->latitude . '\',
                    longitude: \'' . $osmConfig->default_location->longitude . '\',
                    zoom: \'' . $osmConfig->default_location->zoom . '\',
                },
            };
        </script>';
    }
}

