<?php

namespace Slince\ShipmentTracking\Tests\FourPartyExpress;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\FourPartyExpress\FourPartyExpressTracker;

class FourPartyExpressTrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return FourPartyExpressTracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(FourPartyExpressTracker::class)
            ->setMethods(['sendRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $tracker->method('sendRequest')
            ->willReturn(new Response(200,  [], file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.json')));
        return $tracker;
    }

    public function testTrack()
    {
        $shipment = $this->getTrackerMock('valid_track')->track('800004-161210-2511');
        $this->assertCount(4, $shipment->getEvents());
    }

    public function testBad()
    {
        $this->expectException(TrackException::class);
        $shipment = $this->getTrackerMock('bad_response')->track('800004-161210-2511');
    }

//    public function testAccess()
//    {
//        $tracker = new FourPartyExpressTracker('70678f85-773e-458a-8211-bf3064774c2b2','d4695db2-7753-4f09-a6aa-e2795574d60d');
//        $shipment = $tracker->track('800004-161210-2511');
//    }
}