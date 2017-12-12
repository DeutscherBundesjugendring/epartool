<?php

/**
 * Class Service_OpenStreetMap
 */
class Service_OpenStreetMap
{
    const TILE_SIZE = 256;
    const MAP_IMAGE_EXTENSION = '.png';
    const OSM_ASSETS_PATH = APPLICATION_PATH . '/../assets/common';
    const OSM_LOGO = '/images/osm_logo.png';
    const MAP_TYPE = 'mapnik';
    const TILE_CACHE_BASE_DIR = APPLICATION_PATH . '/../runtime/cache/osm/tiles';
    const MAP_CACHE_BASE_DIR = APPLICATION_PATH . '/../runtime/cache/osm/maps';
    const MARKER_PROTOTYPES = [
        'default' => [
            'image' => '/images/marker_default.png',
            'offsetImage' => [
                'x' => -12,
                'y' => -41,
            ],
            'shadowImage' => '/images/marker_default_shadow.png',
            'offsetShadowImage' => [
                'x' => 0,
                'y' => -13,
            ],
        ],
    ];

    /**
     * @var string
     */
    private $tileSrcUrl;

    /**
     * @param string $tileSrcUrl
     */
    public function __construct($tileSrcUrl)
    {
        $this->tileSrcUrl = $tileSrcUrl;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param int $zoom
     * @param array $markers
     * @throws \Exception
     * @return string
     */
    public function getMap($latitude, $longitude, $width, $height, $zoom, array $markers)
    {
        $fileName = $this->mapCacheIDToFilename(
            $this->getMapCacheId($latitude, $longitude, $width, $height, $zoom, $markers)
        );
        if (!file_exists($fileName)) {
            $image = $this->makeMap($latitude, $longitude, $width, $height, $zoom, $markers);
            $this->mkdirRecursive(dirname($fileName), 0777);
            if (!imagepng($image, $fileName, 9)) {
                throw new \Dbjr_Exception('Cannot write image.');
            }
        }

        return file_get_contents($fileName);
    }

    /**
     * @param float $long
     * @param int $zoom
     * @return float
     */
    private function lonToTile($long, $zoom)
    {
        return (($long + 180) / 360) * pow(2, $zoom);
    }

    /**
     * @param float $lat
     * @param int $zoom
     * @return float
     */
    private function latToTile($lat, $zoom)
    {
        return (1 - log(tan($lat * pi() / 180) + 1 / cos($lat * pi() / 180)) / pi()) / 2 * pow(2, $zoom);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $zoom
     * @return array
     */
    private function initCoords($latitude, $longitude, $zoom)
    {
        $initCords = [];
        $initCords['centerX'] = $this->lonToTile($longitude, $zoom);
        $initCords['centerY'] = $this->latToTile($latitude, $zoom);
        $initCords['offsetX'] = floor((floor($initCords['centerX']) - $initCords['centerX']) * self::TILE_SIZE);
        $initCords['offsetY'] = floor((floor($initCords['centerY']) - $initCords['centerY']) * self::TILE_SIZE);

        return $initCords;
    }

    /**
     * @param array $initCords
     * @param int $width
     * @param int $height
     * @param int $zoom
     * @return resource
     */
    private function createBaseMap(array $initCords, $width, $height, $zoom)
    {
        $image = imagecreatetruecolor($width, $height);
        $startX = floor($initCords['centerX'] - ($width / self::TILE_SIZE) / 2);
        $startY = floor($initCords['centerY'] - ($height / self::TILE_SIZE) / 2);
        $endX = ceil($initCords['centerX'] + ($width / self::TILE_SIZE) / 2);
        $endY = ceil($initCords['centerY'] + ($height / self::TILE_SIZE) / 2);
        $offsetX = - floor(($initCords['centerX'] - floor($initCords['centerX'])) * self::TILE_SIZE);
        $offsetY = - floor(($initCords['centerY'] - floor($initCords['centerY'])) * self::TILE_SIZE);
        $offsetX += floor($width / 2);
        $offsetY += floor($height / 2);
        $offsetX += floor($startX - floor($initCords['centerX'])) * self::TILE_SIZE;
        $offsetY += floor($startY - floor($initCords['centerY'])) * self::TILE_SIZE;

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $url = str_replace(array('{z}', '{x}', '{y}'), array($zoom, $x, $y), $this->tileSrcUrl);
                $tileData = $this->fetchTile($url);
                if ($tileData) {
                    $tileImage = imagecreatefromstring($tileData);
                } else {
                    $tileImage = imagecreate(self::TILE_SIZE, self::TILE_SIZE);
                    $color = imagecolorallocate($tileImage, 255, 255, 255);
                    @imagestring($tileImage, 1, 127, 127, 'err', $color);
                }
                $destX = ($x - $startX) * self::TILE_SIZE + $offsetX;
                $destY = ($y - $startY) * self::TILE_SIZE + $offsetY;
                imagecopy($image, $tileImage, $destX, $destY, 0, 0, self::TILE_SIZE, self::TILE_SIZE);
            }
        }

        return $image;
    }

    /**
     * @param resource $image
     * @param array $initCords
     * @param int $width
     * @param int $height
     * @param int $zoom
     * @param array $markers
     */
    private function placeMarkers($image, array $initCords, $width, $height, $zoom, array $markers)
    {
        foreach ($markers as $marker) {
            if (empty($marker['latitude']) || empty($marker['longitude'])) {
                continue;
            }
            if (empty($marker['type']) || !in_array($marker['type'], array_keys(self::MARKER_PROTOTYPES))) {
                $marker['type'] = 'default';
            }
            $markerPrototype = self::MARKER_PROTOTYPES[$marker['type']];
            $markerImg = imagecreatefrompng(
                sprintf('%s/%s', self::OSM_ASSETS_PATH, $markerPrototype['image'])
            );
            $markerShadowImg = null;
            if ($markerPrototype['shadowImage'] !== null) {
                $markerShadowImg = imagecreatefrompng(
                    sprintf('%s/%s', self::OSM_ASSETS_PATH, $markerPrototype['shadowImage'])
                );
            }
            $destX = floor(($width / 2) - self::TILE_SIZE
                * ($initCords['centerX'] - $this->lonToTile($marker['longitude'], $zoom)));
            $destY = floor(($height / 2) - self::TILE_SIZE
                * ($initCords['centerY'] - $this->latToTile($marker['latitude'], $zoom)));
            if ($markerShadowImg !== null) {
                imagecopy(
                    $image,
                    $markerShadowImg,
                    $destX + $markerPrototype['offsetShadowImage']['x'],
                    $destY + $markerPrototype['offsetShadowImage']['y'],
                    0,
                    0,
                    imagesx($markerShadowImg),
                    imagesy($markerShadowImg)
                );
            }
            imagecopy(
                $image,
                $markerImg,
                $destX + $markerPrototype['offsetImage']['x'],
                $destY + $markerPrototype['offsetImage']['y'],
                0,
                0,
                imagesx($markerImg),
                imagesy($markerImg)
            );
        };
    }

    /**
     * @param string $url
     * @return string
     */
    private function tileUrlToFilename($url)
    {
        return self::TILE_CACHE_BASE_DIR . "/" . str_replace(['http://', 'https://'], '', $url);
    }

    /**
     * @param string $url
     * @return null|string
     */
    private function checkTileCache($url)
    {
        $filename = $this->tileUrlToFilename($url);
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }

        return null;
    }

    /**
     * @param int $zoom
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param array $markers
     * @return string
     */
    private function serializeParams($zoom, $latitude, $longitude, $width, $height, $markers)
    {
        return join("&", [$zoom, $latitude, $longitude, $width, $height, serialize($markers)]);
    }

    /**
     * @param string $mapCacheId
     * @return string
     */
    private function mapCacheIDToFilename($mapCacheId)
    {
        return sprintf(
            '%s/%s/cache_%s/%s/%s',
            self::MAP_CACHE_BASE_DIR,
            self::MAP_TYPE,
            substr($mapCacheId, 0, 2),
            substr($mapCacheId, 2, 2),
            substr($mapCacheId, 4)
        );
    }

    /**
     * @param string $pathname
     * @param int $mode
     * @return bool
     */
    private function mkdirRecursive($pathname, $mode)
    {
        is_dir(dirname($pathname)) || $this->mkdirRecursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    /**
     * @param string $url
     * @param string $data
     */
    private function writeTileToCache($url, $data)
    {
        $filename = $this->tileUrlToFilename($url);
        $this->mkdirRecursive(dirname($filename), 0777);
        file_put_contents($filename, $data);
    }

    /**
     * @param string $url
     * @return mixed
     */
    private function fetchTile($url)
    {
        if (null !== ($cached = $this->checkTileCache($url))) {
            return $cached;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($ch, CURLOPT_URL, $url);
        $tile = curl_exec($ch);
        curl_close($ch);
        if ($tile) {
            $this->writeTileToCache($url, $tile);
        }
        return $tile;
    }

    /**
     * @param resource $image
     */
    private function copyrightNotice($image)
    {
        $logoImg = imagecreatefrompng(self::OSM_ASSETS_PATH . '/' . self::OSM_LOGO);
        imagecopy(
            $image,
            $logoImg,
            imagesx($image) - imagesx($logoImg),
            imagesy($image) - imagesy($logoImg),
            0,
            0,
            imagesx($logoImg),
            imagesy($logoImg)
        );
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param int $zoom
     * @param array $markers
     * @return resource
     */
    private function makeMap($latitude, $longitude, $width, $height, $zoom, $markers)
    {
        $initCords = $this->initCoords($latitude, $longitude, $zoom);
        $image = $this->createBaseMap($initCords, $width, $height, $zoom);
        if (count($markers)) {
            $this->placeMarkers($image, $initCords, $width, $height, $zoom, $markers);
        }
        $this->copyrightNotice($image);

        return $image;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param int $zoom
     * @param array $markers
     * @return string
     */
    private function getMapCacheId($latitude, $longitude, $width, $height, $zoom, $markers)
    {
        return md5($this->serializeParams($latitude, $longitude, $width, $height, $zoom, $markers));
    }
}
