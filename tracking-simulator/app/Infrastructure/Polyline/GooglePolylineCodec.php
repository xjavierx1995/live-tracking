<?php

namespace App\Infrastructure\Polyline;

use App\Application\Contracts\PolylineCodec;
use App\Domain\Simulation\GeoPoint;

class GooglePolylineCodec implements PolylineCodec
{
    public function encode(array $points): string
    {
        $result = '';
        $previousLatitude = 0;
        $previousLongitude = 0;

        foreach ($points as $point) {
            $latitude = (int) round($point->latitude * 1e5);
            $longitude = (int) round($point->longitude * 1e5);

            $result .= $this->encodeValue($latitude - $previousLatitude);
            $result .= $this->encodeValue($longitude - $previousLongitude);

            $previousLatitude = $latitude;
            $previousLongitude = $longitude;
        }

        return $result;
    }

    public function decode(string $polyline): array
    {
        $points = [];
        $index = 0;
        $latitude = 0;
        $longitude = 0;
        $length = strlen($polyline);

        while ($index < $length) {
            $latitude += $this->decodeValue($polyline, $index);
            $longitude += $this->decodeValue($polyline, $index);

            $points[] = new GeoPoint($latitude / 1e5, $longitude / 1e5);
        }

        return $points;
    }

    private function encodeValue(int $value): string
    {
        $value = $value < 0 ? ~($value << 1) : $value << 1;
        $encoded = '';

        while ($value >= 0x20) {
            $encoded .= chr((0x20 | ($value & 0x1f)) + 63);
            $value >>= 5;
        }

        return $encoded.chr($value + 63);
    }

    private function decodeValue(string $polyline, int &$index): int
    {
        $shift = 0;
        $result = 0;

        do {
            $byte = ord($polyline[$index++]) - 63;
            $result |= ($byte & 0x1f) << $shift;
            $shift += 5;
        } while ($byte >= 0x20);

        return ($result & 1) !== 0 ? ~($result >> 1) : ($result >> 1);
    }
}
