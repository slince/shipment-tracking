<?php
namespace Slince\ShipmentTracking\Tests\KuaiDi100;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\KuaiDi100\KuaiDi100Tracker;

class KuaiDi100TrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return KuaiDi100Tracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(KuaiDi100Tracker::class)
            ->setMethods(['sendRequest'])
            ->setConstructorArgs(['foo', 'en'])
            ->getMock();
        $tracker->method('sendRequest')
            ->willReturn(\GuzzleHttp\json_decode(file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.json'), true));
        return $tracker;
    }

    public function testSetter()
    {
        $tracker = new KuaiDi100Tracker('foo', 'sto');
        $this->assertEquals('foo', $tracker->getAppKey());
        $this->assertEquals('sto', $tracker->getCarrier());
        $tracker->setAppKey('bar');
        $tracker->setCarrier('yto');
        $this->assertEquals('bar', $tracker->getAppKey());
        $this->assertEquals('yto', $tracker->getCarrier());
    }

    public function testTrack()
    {
        $tracker = $this->getTrackerMock('valid_tracking');
        $shipment = $tracker->track('foo');
        $this->assertTrue($shipment->isDelivered());
        $this->assertCount(9, $shipment->getEvents());
    }

    public function testErrorTrack()
    {
        $tracker = $this->getTrackerMock('invalid_tracking');
        $this->expectException(TrackException::class);
        $tracker->track('foo');
    }
}