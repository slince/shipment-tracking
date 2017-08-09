<?php
namespace Slince\ShipmentTracking\Tests\DHLECommerce;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\DHLECommerce\Credential;

class CredentialTest extends TestCase
{
    public function testConstructor()
    {
        $credential = new Credential('foo', 'bar');
        $this->assertEquals('foo', $credential->getClientId());
        $this->assertEquals('bar', $credential->getPassword());
    }

    public function testSetter()
    {
        $credential = new Credential('foo', 'bar');
        $credential->setClientId('bar');
        $credential->setPassword('baz');
        $this->assertEquals('bar', $credential->getClientId());
        $this->assertEquals('baz', $credential->getPassword());
    }

    public function testToArray()
    {
        $credential = new Credential('foo', 'bar');
        $this->assertEquals([
            'clientId' => 'foo',
            'password' => 'bar'
        ], $credential->toArray());
    }
}