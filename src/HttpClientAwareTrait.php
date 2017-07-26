<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracker;

use GuzzleHttp\Client as HttpClient;

trait HttpClientAwareTrait
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Sets the http client
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}