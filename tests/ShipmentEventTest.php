<?php
namespace Slince\ShipmentTracking\Tests;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\ShipmentEvent;

class ShipmentEventTest extends TestCase
{
    public function testConstructor()
    {
        $now = new \DateTime();
        $event = new ShipmentEvent($now, 'foo', 'bar');
        $this->assertEquals($now, $event->getDate());
        $this->assertEquals('foo', $event->getDescription());
        $this->assertEquals('bar', $event->getLocation());
    }

    public function testSetter()
    {
        $event = new ShipmentEvent();
        $this->assertNull($event->getLocation());
        $this->assertNull($event->getDate());
        $this->assertNull($event->getDescription());
        $this->assertNull($event->getStatus());

        $now = new \DateTime();
        $event->setDate($now);
        $event->setDescription('foo');
        $event->setLocation('bar');
        $event->setStatus('baz');

        $this->assertEquals($now, $event->getDate());
        $this->assertEquals('foo', $event->getDescription());
        $this->assertEquals('bar', $event->getLocation());
        $this->assertEquals('baz', $event->getStatus());
    }

    public function testFromArray()
    {
        $event = ShipmentEvent::fromArray([
            'description' => 'foo',
            'location' => 'bar'
        ]);
        $this->assertEquals('foo', $event->getDescription());
        $this->assertEquals('bar', $event->getLocation());
    }

    public function testToArray()
    {
        $event = new ShipmentEvent();
        $this->assertEquals([
            'description' => null,
            'location' => null,
            'date' => null,
            'status' => null
        ], $event->toArray());
    }

    public function testJson()
    {
        $event = new ShipmentEvent();
        $json = json_encode($event);
        $array = json_decode($json, true);
        $this->assertNotEmpty($array);
    }
}