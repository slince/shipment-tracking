<?php
namespace Slince\ShipmentTracking\Tests\USPS;

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
            ->willReturn(file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.xml'));
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
        $this->assertCount(13, $shipment->getEvents());
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
}