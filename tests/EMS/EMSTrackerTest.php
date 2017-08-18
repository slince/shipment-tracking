<?php
namespace Slince\ShipmentTracking\Tests\EMS;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Foundation\Exception\InvalidArgumentException;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\EMS\EMSTracker;

class EMSTrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return EMSTracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(EMSTracker::class)
            ->setMethods(['sendRequest'])
            ->setConstructorArgs(['foo', 'en'])
            ->getMock();
        $tracker->method('sendRequest')
            ->willReturn(file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.xml'), true);
        return $tracker;
    }

    public function testVersion()
    {
        $this->assertEquals('international_eub_us_1.1', EMSTracker::getVersion());
        EMSTracker::setVersion('foo');
        $this->assertEquals('foo', EMSTracker::getVersion());
    }

    public function testSetter()
    {
        $tracker = new EMSTracker('foo', 'en');
        $this->assertEquals('en', $tracker->getLanguage());
        $this->assertEquals('foo', $tracker->getAuthenticate());
        $tracker->setLanguage('cn');
        $tracker->setAuthenticate('bar');
        $this->assertEquals('cn', $tracker->getLanguage());
        $this->assertEquals('bar', $tracker->getAuthenticate());

        $this->expectException(InvalidArgumentException::class);
        $tracker->setLanguage('es');
    }

    public function testTrack()
    {
        $tracker = $this->getTrackerMock('valid_tracking');
        $shipment = $tracker->track('foo');
        $this->assertNull($shipment->isDelivered());
        $this->assertCount(7, $shipment->getEvents());
    }

    public function testErrorTrack()
    {
        $tracker = $this->getTrackerMock('invalid_tracking');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }

    public function testOneTrace()
    {
        $tracker = $this->getTrackerMock('one_trace_track');
        $shipment = $tracker->track('foo');
        $this->assertNull($shipment->isDelivered());
        $this->assertCount(1, $shipment->getEvents());
    }

    public function testInvalidAuthenticate()
    {
        $tracker = $this->getTrackerMock('invalid_authenticate');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }
}