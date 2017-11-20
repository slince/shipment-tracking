<?php
namespace Slince\ShipmentTracking\Tests\USPS;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\USPS\USPSTracker;

class USPSTrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return USPSTracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(USPSTracker::class)
            ->setMethods(['request'])
            ->setConstructorArgs(['foo'])
            ->getMock();
        $tracker->method('request')
            ->willReturn(new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.xml')));
        return $tracker;
    }

    public function testSetter()
    {
        $tracker = new USPSTracker('foo');
        $this->assertEquals('foo', $tracker->getUserId());
        $tracker->setUserId('bar');
        $this->assertEquals('bar', $tracker->getUserId());
    }

    public function testTrack()
    {
        $tracker = $this->getTrackerMock('valid_track');
        $shipment = $tracker->track('foo');
        $this->assertTrue($shipment->isDelivered());
        $this->assertCount(14, $shipment->getEvents());
    }

    public function testOneTraceTrack()
    {
        $tracker = $this->getTrackerMock('one_trace_track');
        $shipment = $tracker->track('foo');
        $this->assertTrue($shipment->isDelivered());
        $this->assertCount(2, $shipment->getEvents());
    }

    public function testErrorTrack()
    {
        $tracker = $this->getTrackerMock('invalid_track');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }

    public function testErrorAuth()
    {
        $tracker = $this->getTrackerMock('error_auth');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }

    public function testOneEvent()
    {
        $tracker = $this->getTrackerMock('no_events_track');
        $shipment = $tracker->track('foo');
        $this->assertCount(1, $shipment->getEvents());
    }
}