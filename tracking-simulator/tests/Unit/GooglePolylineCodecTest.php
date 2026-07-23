<?php

namespace Tests\Unit;

use App\Domain\Simulation\GeoPoint;
use App\Infrastructure\Polyline\GooglePolylineCodec;
use PHPUnit\Framework\TestCase;

class GooglePolylineCodecTest extends TestCase
{
    public function test_it_encodes_and_decodes_a_polyline_round_trip(): void
    {
        $codec = new GooglePolylineCodec();
        $points = [
            new GeoPoint(4.7110, -74.0721),
            new GeoPoint(4.7125, -74.0700),
            new GeoPoint(4.7130, -74.0691),
        ];

        $polyline = $codec->encode($points);
        $decoded = $codec->decode($polyline);

        $this->assertCount(3, $decoded);
        $this->assertEqualsWithDelta($points[0]->latitude, $decoded[0]->latitude, 0.00001);
        $this->assertEqualsWithDelta($points[0]->longitude, $decoded[0]->longitude, 0.00001);
        $this->assertEqualsWithDelta($points[2]->latitude, $decoded[2]->latitude, 0.00001);
        $this->assertEqualsWithDelta($points[2]->longitude, $decoded[2]->longitude, 0.00001);
    }
}