<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracker;

use GuzzleHttp\Client as HttpClient;

interface HttpClientAwareInterface
{
    /**
     * Sets the http client
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient);
}