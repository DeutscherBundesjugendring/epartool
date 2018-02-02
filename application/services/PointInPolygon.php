<?php

class Service_PointInPolygon
{
    /**
     * @param float $latitude
     * @param float $longitude
     * @param array[] $polygonCoordinates
     * @return bool
     */
    public function isPointInPolygon($latitude, $longitude, $polygonCoordinates)
    {
        return (new \Geometry\Polygon($polygonCoordinates))->pip($latitude, $longitude);
    }
}
