<?php
namespace Slince\ShipmentTracking\Tests\DHLECommerce;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\DHLECommerce\AccessToken;
use Slince\ShipmentTracking\DHLECommerce\DHLECommerceTracker;
use Slince\ShipmentTracking\DHLECommerce\Exception\InvalidAccessTokenException;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;

class DHLECommerceTrackerTest extends TestCase
{
    /**
     * @param string $fixture
     * @return DHLECommerceTracker
     */
    protected function getTrackerMock($fixture)
    {
        $tracker = $this->getMockBuilder(DHLECommerceTracker::class)
            ->setMethods(['sendRequest'])
            ->setConstructorArgs(['foo', 'bar'])
            ->getMock();
        $tracker->method('sendRequest')
            ->willReturn(\GuzzleHttp\json_decode(file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.json'), true));
        return $tracker;
    }

    public function testCredential()
    {
        $tracker = new DHLECommerceTracker('foo', 'bar');
        $this->assertEquals([
            'clientId' => 'foo',
            'password' => 'bar'
        ], $tracker->getCredential()->toArray());
    }

    public function testAccessToken()
    {
        $tracker = $this->getTrackerMock('get_access_token_response');
        $accessToken = $tracker->getAccessToken();
        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('89439b80asda09a2as168f94d6ffsssfefa6e0139123', $accessToken->getToken());
        $this->assertEquals('Bearer', $accessToken->getType());
        $this->assertEquals(82805, $accessToken->getExpiresIn());
    }

    public function testSetAccessToken()
    {
        $tracker = $this->getTrackerMock('get_access_token_response');
        $tracker->setAccessToken('foo');
        $this->assertEquals('foo', $tracker->getAccessToken());
        $this->assertEquals('foo', $tracker->getAccessToken()->getToken());
        $this->assertNull($tracker->getAccessToken()->getType());
        $this->assertNull($tracker->getAccessToken()->getExpiresIn());
    }

    public function testErrorGetAccessToken()
    {
        $tracker = $this->getTrackerMock('bad_get_access_token_response');
        $this->expectException(TrackException::class);
        $tracker->getAccessToken();
    }

    public function testTrack()
    {
        $tracker = $this->getTrackerMock('tracking_with_valid_access_token_response');
        $tracker->setAccessToken('foo');
        $shipment = $tracker->track('foo');
        $this->assertFalse($shipment->isDelivered());
        $this->assertCount(7, $shipment->getEvents());
    }

    public function testErrorTrack()
    {
        $tracker = $this->getTrackerMock('tracking_with_invalid_access_token_response');
        $tracker->setAccessToken('foo');
        $this->expectException(InvalidAccessTokenException::class);
        $tracker->track('foo');
    }
}