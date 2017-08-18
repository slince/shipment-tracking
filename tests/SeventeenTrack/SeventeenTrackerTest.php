<?php
namespace Slince\ShipmentTracking\Tests\SeventeenTrack;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\SeventeenTrack\SeventeenTracker;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;

class SeventeenTrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return SeventeenTracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(SeventeenTracker::class)
            ->setMethods(['sendRequest'])
            ->getMock();
        $tracker->method('sendRequest')
            ->willReturn(new Response(200,  [], file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.json')));
        return $tracker;
    }


    public function testTrack()
    {
        $tracker = $this->getTrackerMock('valid_track');
        $shipment = $tracker->track('foo');
        $this->assertTrue($shipment->isDelivered());
        $this->assertCount(21, $shipment->getEvents());
        $this->assertCount(6, $shipment->getOriginEvents());
        $this->assertCount(15, $shipment->getDestinationEvents());
    }

    public function testErrorTrack()
    {
        $tracker = $this->getTrackerMock('invalid_track');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }

    public function testRequest()
    {
        $tracker = new SeventeenTracker();
        $shipment = $tracker->track('LW489083733CN');
        $this->assertNotEmpty($shipment);
        $this->assertCount(21, $shipment->getEvents());
        $this->assertCount(6, $shipment->getOriginEvents());
        $this->assertCount(15, $shipment->getDestinationEvents());
        $this->assertTrue($shipment->isDelivered());
    }
}