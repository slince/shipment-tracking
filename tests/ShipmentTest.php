<?php
namespace Slince\ShipmentTracking\Tests;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Exception\TrackException;
use Slince\ShipmentTracking\Shipment;
use Slince\ShipmentTracking\ShipmentEvent;

class ShipmentTest extends TestCase
{
    public function testConstructor()
    {
        $shipment = new Shipment([ShipmentEvent::fromArray([
            'location' => 'Foo',
            'description' => 'bar'
        ])], false);
        $this->assertFalse($shipment->isDelivered());
        $this->assertCount(1, $shipment->getEvents());
    }

    public function testWeight()
    {
        $shipment = new Shipment();
        $this->assertNull($shipment->getWeight());
        $this->assertNull($shipment->getWeightUnit());
        $shipment->setWeight(1.2);
        $shipment->setWeightUnit('kg');
        $this->assertEquals(1.2, $shipment->getWeight());
        $this->assertEquals('kg', $shipment->getWeightUnit());
    }

    public function testOriginAndDestination()
    {
        $shipment = new Shipment();
        $this->assertNull($shipment->getOrigin());
        $this->assertNull($shipment->getDestination());
        $shipment->setOrigin('China');
        $shipment->setDestination('US');
        $this->assertEquals('China', $shipment->getOrigin());
        $this->assertEquals('US', $shipment->getDestination());
    }

    public function testDelivered()
    {
        $shipment = new Shipment();
        $this->assertNull($shipment->isDelivered());
        $shipment->setIsDelivered(true);
        $this->assertTrue($shipment->isDelivered());
    }

    public function testDeliveryAt()
    {
        $shipment = new Shipment();
        $this->assertNull($shipment->getDeliveredAt());
        $shipment->setDeliveredAt(new \DateTime());
        $this->assertNotNull($shipment->getDeliveredAt());
    }

    public function testStatus()
    {
        $shipment = new Shipment();
        $this->assertNull($shipment->getStatus());
        $shipment->setStatus('foo');
        $this->assertNotNull($shipment->getStatus());
    }

    public function testEvents()
    {
        $shipment = new Shipment();
        $events = [ShipmentEvent::fromArray([
            'location' => 'Foo',
            'description' => 'bar'
        ])];
        $this->assertCount(0, $shipment->getEvents());
        $shipment->setEvents($events);
        $this->assertCount(1, $shipment->getEvents());
    }

    public function testJson()
    {
        $shipment = new Shipment();
        $json = json_encode($shipment);
        $array = json_decode($json, true);
        $this->assertNotEmpty($array);
    }
}