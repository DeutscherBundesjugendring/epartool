<?php

/**
 * Class Api_OpenStreetMapController
 */
class Api_OpenStreetMapController extends Dbjr_Api_BaseController
{
    const BROWSER_CACHE_EXPIRES = 60 * 60 * 24 * 14;
    const DEFAULT_WIDTH = 300;
    const DEFAULT_HEIGHT = 200;
    const MAX_WIDTH = 1024;
    const MAX_HEIGHT = 1024;
    const MAX_ZOOM = 18;

    public function staticMapAction()
    {
        $params = $this->getAllParams();
        $osmConfig = Zend_Registry::get('systemconfig')->osm;

        try {
            $parsedParams = $this->parseParams($params, $osmConfig->default_location->zoom);
            $osmService = new Service_OpenStreetMap($osmConfig->data_server_url);

            $mapImageContent = $osmService->getMap(
                $parsedParams['latitude'],
                $parsedParams['longitude'],
                $parsedParams['width'],
                $parsedParams['height'],
                $parsedParams['zoom'],
                $parsedParams['markers']
            );
            $this->sendHeader();
            if (false === file_put_contents('php://output', $mapImageContent)) {
                throw new Dbjr_Exception(self::HTTP_STATUS_BAD_REQUEST, 'Cannot send the content.');
            }
            exit;
        } catch (Dbjr_Api_Exception $e) {
            $this->sendError($e->getHttpStatusCode(), $e->getMessage());
        } catch (Dbjr_Exception $e) {
            $this->sendError(self::HTTP_STATUS_SERVER_ERROR, 'Server error.');
        }
    }

    private function sendHeader()
    {
        header('Content-Type: image/png');
        header("Pragma: public");
        header("Cache-Control: maxage=" . self::BROWSER_CACHE_EXPIRES);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + self::BROWSER_CACHE_EXPIRES) . ' GMT');
    }

    /**
     * @param array $params
     * @param int $defaultZoom
     * @throws \Dbjr_Api_Exception
     * @return array
     */
    private function parseParams(array $params, $defaultZoom)
    {
        $zoom = $params['zoom'] ? intval($params['zoom']) : $defaultZoom;
        if ($zoom > self::MAX_ZOOM) {
            throw new Dbjr_Exception(sprintf('Zoom %d (over %d) is not allowed', $zoom, self::MAX_ZOOM));
        }

        list($latitude, $longitude) = explode(',', $params['center']);
        if (empty($latitude) || empty($longitude)) {
            throw new Dbjr_Api_Exception(
                self::HTTP_STATUS_BAD_REQUEST,
                'Latitude and longitude of the center of the map must be defined.'
            );
        }

        $width = self::DEFAULT_WIDTH;
        $height = self::DEFAULT_HEIGHT;
        if ($params['size']) {
            list($width, $height) = explode('x', $params['size']);

            $width = intval($width);
            if ($width > self::MAX_WIDTH) {
                throw new Dbjr_Exception(sprintf('Width %d (over %d) is not allowed', $width, self::MAX_WIDTH));
            }
            $height = intval($height);
            if ($height > self::MAX_HEIGHT) {
                throw new Dbjr_Exception(sprintf('Height %d (over %d) is not allowed', $height, self::MAX_HEIGHT));
            }
        }

        $markers = [];
        if (isset($params['markers'])) {
            $markersDefinitions = explode('|', $params['markers']);
            foreach ($markersDefinitions as $marker) {
                $data = explode(',', $marker);
                $markers[] = [
                    'latitude' => floatval($data[0]),
                    'longitude' => floatval($data[1]),
                    'type' => (isset($data[2]) ? basename($data) : 'default'),
                ];
            }
        }

        return [
            'zoom' => (int) $zoom,
            'width' => (int) $width,
            'height' => (int) $height,
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'markers' => $markers,
        ];
    }
}
