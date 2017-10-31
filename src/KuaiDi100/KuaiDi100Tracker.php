<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\KuaiDi100;

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use Slince\ShipmentTracking\Foundation\Shipment;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;

class KuaiDi100Tracker extends HttpAwareTracker
{
    /**
     * @var string
     */
    const TRACKING_ENDPOINT = 'http://api.kuaidi100.com/api';

    /**
     * @var string
     */
    protected $appKey;

    /**
     * @var string
     */
    protected $carrier;

    public function __construct($appKey, $carrier = null, HttpClient $httpClient = null)
    {
        $this->appKey = $appKey;
        $this->carrier = $carrier;
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * @param string $appKey
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @return string
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param string $carrier
     * @return KuaiDi100Tracker
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
        return $this;
    }

    /**
     * @param string $trackingNumber
     * @return array
     */
    protected function buildQueryParameters($trackingNumber)
    {
        return [
            'id' => $this->appKey,
            'com' => $this->carrier,
            'nu' => $trackingNumber,
            'show' => 0,
            'multi' => 1,
            'order' => 'asc',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $request = new Request('GET', static::TRACKING_ENDPOINT);
        $json = $this->sendRequest($request, [
            'query' => $this->buildQueryParameters($trackingNumber)
        ]);
        if ($json['status'] != 1) {
            throw new TrackException(sprintf('Bad response with status "%d"', $json['status']));
        }
        return static::buildShipment($json);
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return array
     * @codeCoverageIgnore
     */
    protected function sendRequest(RequestInterface $request, array $options = [])
    {
        try {
            $response = $this->getHttpClient()->send($request, $options);
            return \GuzzleHttp\json_decode((string)$response->getBody(), true);
        } catch (GuzzleException $exception) {
            throw new TrackException($exception->getMessage());
        }
    }

    /**
     * @param array $json
     * @return Shipment
     */
    protected static function buildShipment($json)
    {
        $events = array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => $item['location'],
                'description' => $item['context'],
                'date' => Carbon::parse($item['time']),
            ]);
        }, $json['data']);
        $shipment = new Shipment($events);
        $shipment->setIsDelivered($json['state'] == 3);
        if ($firstEvent = reset($events)) {
            $shipment->setDeliveredAt($firstEvent->getDate());
        }
        return $shipment;
    }
}