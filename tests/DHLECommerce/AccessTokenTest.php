<?php
namespace Slince\ShipmentTracking\Tests\DHLECommerce;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\DHLECommerce\AccessToken;

class AccessTokenTest extends TestCase
{
    public function testConstructor()
    {
        $accessToken = new AccessToken('foo', 'bar', 100);
        $this->assertEquals('foo', $accessToken);
        $this->assertEquals('foo', $accessToken->getToken());
        $this->assertEquals('bar', $accessToken->getType());
        $this->assertEquals(100, $accessToken->getExpiresIn());
    }

    public function testSetter()
    {
        $accessToken = new AccessToken('foo', 'bar', 100);

        $accessToken->setToken('bar');
        $accessToken->setType('baz');
        $accessToken->setExpiresIn(1000);

        $this->assertEquals('bar', $accessToken);
        $this->assertEquals('bar', $accessToken->getToken());
        $this->assertEquals('baz', $accessToken->getType());
        $this->assertEquals(1000, $accessToken->getExpiresIn());
    }
}