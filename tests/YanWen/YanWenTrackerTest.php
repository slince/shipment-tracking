<?php
namespace Slince\ShipmentTracking\Tests\YanWenExpress;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\YanWenExpress\YanWenTracker;

class YanWenTrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return YanWenTracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(YanWenTracker::class)
            ->setMethods(['sendRequest'])
            ->setConstructorArgs(['foo', 'en'])
            ->getMock();
        $tracker->method('sendRequest')
            ->willReturn(\GuzzleHttp\json_decode(file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.json'), true));
        return $tracker;
    }

    public function testSetter()
    {
        $tracker = new YanWenTracker('foo', 'en');
        $this->assertEquals('foo', $tracker->getKey());
        $this->assertEquals('en', $tracker->getCulture());
        $tracker->setKey('bar');
        $tracker->setCulture('cn');
        $this->assertEquals('bar', $tracker->getKey());
        $this->assertEquals('cn', $tracker->getCulture());
    }

    public function testTrack()
    {
        $tracker = $this->getTrackerMock('valid_tracking');
        $shipment = $tracker->track('foo');
        $this->assertTrue($shipment->isDelivered());
        $this->assertCount(22, $shipment->getEvents());
        $this->assertCount(10, $shipment->getOriginEvents());
        $this->assertCount(12, $shipment->getDestinationEvents());
    }

    public function testErrorTrack()
    {
        $tracker = $this->getTrackerMock('invalid_tracking');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }

    public function testBadResponseTrack()
    {
        $tracker = $this->getTrackerMock('error_tracking_number_tracking');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }
}