<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\HttpAwareTracker;
use Slince\ShipmentTracking\TrackerInterface;

class HttpAwareTrackerTest extends TestCase
{
    public function testHttpClient()
    {
        $tracker = $this->getMockBuilder(HttpAwareTracker::class)->getMockForAbstractClass();
        $this->assertObjectHasAttribute('httpClient',$tracker);
        $this->assertAttributeEmpty('httpClient', $tracker);
        $tracker->setHttpClient(new Client());
        $this->assertAttributeInstanceOf(Client::class, 'httpClient', $tracker);
        $this->assertInstanceOf(TrackerInterface::class, $tracker);
    }
}