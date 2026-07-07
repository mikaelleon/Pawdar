<?php

/**
 * Converts latitude/longitude to global pixel coordinates at a zoom level.
 *
 * @return array{x: float, y: float}
 */
function map_thumbnail_lat_lng_to_pixel(float $latitude, float $longitude, int $zoom): array
{
    $scale = 256 * (2 ** $zoom);
    $x = ($longitude + 180.0) / 360.0 * $scale;
    $sinLatitude = sin(deg2rad($latitude));
    $y = (0.5 - log((1 + $sinLatitude) / (1 - $sinLatitude)) / (4 * M_PI)) * $scale;

    return ['x' => $x, 'y' => $y];
}

/**
 * Downloads an OpenStreetMap raster tile.
 */
function map_thumbnail_fetch_tile(int $zoom, int $tileX, int $tileY): ?GdImage
{
    $url = 'https://tile.openstreetmap.org/' . $zoom . '/' . $tileX . '/' . $tileY . '.png';
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: Pawdar/1.0 (local civic app; contact: support@pawdar.local)\r\n",
            'timeout' => 8,
        ],
    ]);

    $binary = @file_get_contents($url, false, $context);
    if ($binary === false || $binary === '') {
        return null;
    }

    $image = @imagecreatefromstring($binary);

    return $image instanceof GdImage ? $image : null;
}

/**
 * Renders a cropped static map preview centered on coordinates.
 */
function render_incident_map_thumbnail(float $latitude, float $longitude, int $width = 280, int $height = 168): bool
{
    if (!function_exists('imagecreatetruecolor')) {
        return false;
    }

    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        return false;
    }

    $width = max(120, min(640, $width));
    $height = max(88, min(360, $height));
    $zoom = 15;

    $center = map_thumbnail_lat_lng_to_pixel($latitude, $longitude, $zoom);
    $topLeftX = $center['x'] - ($width / 2);
    $topLeftY = $center['y'] - ($height / 2);

    $startTileX = (int) floor($topLeftX / 256);
    $startTileY = (int) floor($topLeftY / 256);
    $endTileX = (int) floor(($topLeftX + $width - 1) / 256);
    $endTileY = (int) floor(($topLeftY + $height - 1) / 256);

    $canvas = imagecreatetruecolor($width, $height);
    if ($canvas === false) {
        return false;
    }

    $background = imagecolorallocate($canvas, 108, 139, 159);
    imagefilledrectangle($canvas, 0, 0, $width, $height, $background);

    $tilesLoaded = 0;

    for ($tileY = $startTileY; $tileY <= $endTileY; $tileY++) {
        for ($tileX = $startTileX; $tileX <= $endTileX; $tileX++) {
            $tile = map_thumbnail_fetch_tile($zoom, $tileX, $tileY);
            if ($tile === null) {
                continue;
            }

            $destX = (int) round(($tileX * 256) - $topLeftX);
            $destY = (int) round(($tileY * 256) - $topLeftY);
            imagecopy($canvas, $tile, $destX, $destY, 0, 0, 256, 256);
            imagedestroy($tile);
            $tilesLoaded++;
        }
    }

    if ($tilesLoaded === 0) {
        imagedestroy($canvas);

        return false;
    }

    $markerX = (int) round($center['x'] - $topLeftX);
    $markerY = (int) round($center['y'] - $topLeftY);
    $markerOuter = imagecolorallocate($canvas, 224, 118, 94);
    $markerInner = imagecolorallocate($canvas, 255, 255, 255);
    imagefilledellipse($canvas, $markerX, $markerY, 14, 14, $markerOuter);
    imagefilledellipse($canvas, $markerX, $markerY, 6, 6, $markerInner);

    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');

    imagepng($canvas);
    imagedestroy($canvas);

    return true;
}
