<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

use GuzzleHttp\Client as HttpClient;

interface HttpClientAwareInterface
{
    /**
     * Sets the http client
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient);
}