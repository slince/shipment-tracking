<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\FourPartyExpress;

use Carbon\Carbon;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use Slince\ShipmentTracking\Foundation\Shipment;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;
use GuzzleHttp\Client as HttpClient;

class FourPartyExpressTracker extends HttpAwareTracker
{
    protected $appKey;

    protected $appSecret;

    public function __construct($appKey, $appSecret, HttpClient $httpClient = null)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $response = $this->sendRequest($trackingNumber);
        return static::parseBody($response->getBody());
    }

    protected function sendRequest($trackingNumber)
    {
        $requestUrl = static::generateUrl($this->appKey, $this->appSecret, $trackingNumber);
        return $this->getHttpClient()->post($requestUrl, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' =>  sprintf('{ "deliveryOrderNo":"%s" }', $trackingNumber),
        ]);
    }

    protected static function parseBody($body)
    {
        $json = \GuzzleHttp\json_decode($body, true);
        if (empty($json['data']) || !$json['result']) {
            throw new TrackException(sprintf('Bad Response message: %s', $json['msg']));
        }
        $trackEvents = array_map(function($trackInfo){
            return ShipmentEvent::fromArray([
                'time' => Carbon::parse($trackInfo['occurDatetime']),
                'location' => isset($trackInfo['occurLocation']) ? $trackInfo['occurLocation'] : $trackInfo['businessLinkCode'],
                'description' => $trackInfo['trackingContent']
            ]);
        }, array_reverse($json['data']['trackingList']));
        $shipment = new Shipment($trackEvents);
        $shipment->setDestination($json['data']['destinationCountry']);
        return $shipment;
    }

    /**
     * 生成sign
     * @param string $appKey
     * @param string $appSecret
     * @param string $timestamp
     * @param string $trackingNumber
     * @return string
     */
    protected static function makeSign($appKey, $appSecret, $timestamp, $trackingNumber)
    {
        $signatureStringBuffer = [
            'app_key', $appKey,
            'format', 'json',
            'method', 'tr.order.tracking.get',
            'timestamp', $timestamp,
            'v', '1.0',
            sprintf('{ "deliveryOrderNo":"%s" }', $trackingNumber),
            $appSecret
        ];
        return strtoupper(md5(implode('', $signatureStringBuffer)));
    }

    protected static function generateUrl($appKey, $appSecret, $trackingNumber)
    {
        $timestamp =  Carbon::now()->format('Y-m-d H24:i:s');
        $sign = static::makeSign($appKey, $appSecret, $timestamp, $trackingNumber);
        return sprintf('http://open.4px.com/router/api/service?method=tr.order.tracking.get&v=1.0&app_key=%s&timestamp=%s&format=json&access_token=&sign=%s',
            $appKey,
            $timestamp,
            $sign
        );
    }
}