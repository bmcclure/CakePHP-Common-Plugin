<?php
namespace Nodes;

class Geo {

/**
 * Mean raidus of the earth in kilometers.
 *
 * @var double
 */
	const EARTH_RADIS = 6372.797;

/**
 * Pi divided by 180 degrees. Calculated with PHP Pi constant.
 *
 * @var double
 */
	const PI180 = 0.017453293;

/**
 * Constant for converting kilometers into mt.
 *
 * @var double
 */
	const MT = 1000;

/**
* Calculate the bounding box from one geo point and $dist degrees out
*
* @param $dist Degress radius in the circle
* @param $lat The latitude of the center point
* @param $lng The longitude of the center point
* @return array
*/
	public static function getBoundary($dist, $lat, $lng) {
		static::_findLatBoundary($dist, $lat, $lat1, $lat2);
		static::_findLonBoundary($dist, $lat, $lng, $lat1, $lat2, $lng1, $lng2);

		return compact('lat1', 'lat2', 'lng1', 'lng2');
	}

/**
 * Calculate distance between two points of latitude and longitude.
 *
 * @param double $lat1 The first point of latitude.
 * @param double $lng1 The first point of longitude.
 * @param double $lat2 The second point of latitude.
 * @param double $lng2 The second point of longitude.
 * @param bool $kilometers Set to false to return in miles.
 * @return double The distance in kilometers or mt, whichever selected.
 */
	public static function getDistance($lat1, $lng1, $lat2, $lng2, $kilometers = true) {
		$lat1	*= self::PI180;
		$lng1	*= self::PI180;
		$lat2	*= self::PI180;
		$lng2	*= self::PI180;

		$dlat	= $lat2 - $lat1;
		$dlong	= $lng2 - $lng1;

		$a		= sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlong / 2) * sin($dlong / 2);
		$c		= 2 * atan2(sqrt($a), sqrt(1 - $a));

		$km		= self::EARTH_RADIS * $c;

		if ($kilometers) {
			return $km;
		}

		return $km * self::MT;
	}

	protected static function _findLatBoundary($dist, $lat, &$lat1, &$lat2) {
		$d = ($dist / static::EARTH_RADIS * 2 * M_PI) * 360;
		$lat1 = $lat - $d;
		$lat2 = $lat + $d;

		if ($lat1 > $lat2) {
			list($lat1, $lat2) = array($lat2, $lat1);
		}
	}

	protected static function _findLonBoundary($dist, $lat, $lng, $lat1, $lat2, &$lng1, &$lng2) {
		$d = $lat - $lat1;

		$d1 = $d / cos(deg2rad($lat1));
		$d2 = $d / cos(deg2rad($lat2));

		$lng1 = min($lng - $d1, $lng - $d2);
		$lng2 = max($lng + $d1, $lng + $d2);

		if ($lng1 > $lng2) {
			list($lng1, $lng2) = array($lng2, $lng1);
		}
	}
}