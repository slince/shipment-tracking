<?php
namespace Slince\ShipmentTracking\Tests\USPS;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\USPS\ShipmentEvent;

class ShipmentEventTest extends TestCase
{
    public function testFromArray()
    {
        $event = ShipmentEvent::fromArray([
            'city' => 'foo',
            'country' => 'US',
            'state' => 'bar',
            'time' => '14:00 am',
            'day' => '2017-12-12',
            'date' => '2017-12-12 14:00 am',
            'location' => 'foo bar US',
            'description' => 'foo message',
            'zipCode' => '025112'
        ]);

        $this->assertEquals('foo', $event->getCity());
        $this->assertEquals('bar', $event->getState());
        $this->assertEquals('US', $event->getCountry());
        $this->assertEquals('foo bar US', $event->getLocation());
        $this->assertEquals('foo message', $event->getDescription());
        $this->assertEquals('14:00 am', $event->getTime());
        $this->assertEquals('2017-12-12', $event->getDay());
        $this->assertEquals('2017-12-12 14:00 am', $event->getDate());
        $this->assertEquals('025112', $event->getZipCode());
    }

    public function testSetter()
    {
        $event = ShipmentEvent::fromArray([
            'city' => 'foo',
            'country' => 'US',
            'state' => 'bar',
            'time' => '14:00 am',
            'day' => '2017-12-12',
            'date' => '2017-12-12 14:00 am',
            'location' => 'foo bar US',
            'description' => 'foo message',
            'zipCode' => '025112'
        ]);
        $event->setCity('bar');
        $this->assertEquals('bar', $event->getCity());
        $event->setState('baz');
        $this->assertEquals('baz', $event->getState());
        $event->setCountry('CN');
        $this->assertEquals('CN', $event->getCountry());
        $event->setLocation('JiangSu CN');
        $this->assertEquals('JiangSu CN', $event->getLocation());
        $event->setDescription('bar');
        $this->assertEquals('bar', $event->getDescription());
        $event->setTime('15:30 PM');
        $this->assertEquals('15:30 PM', $event->getTime());
        $event->setDay('2017-08-01');
        $this->assertEquals('2017-08-01', $event->getDay());
        $event->setDate('2017-08-01 15:30');
        $this->assertEquals('2017-08-01 15:30', $event->getDate());
        $event->setZipCode('025115');
        $this->assertEquals('025115', $event->getZipCode());
    }
}