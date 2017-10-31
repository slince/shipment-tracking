<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\YanWenExpress;

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;

class YanWenTracker extends HttpAwareTracker
{
    /**
     * @var string
     */
    const TRACKING_ENDPOINT = 'http://api.track.yw56.com.cn/v2/api/Tracking';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $culture;

    public function __construct($key = 'none', $culture =  'en', HttpClient $httpClient = null)
    {
        $this->key = $key;
        $this->culture = $culture;
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return YanWenTracker
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getCulture()
    {
        return $this->culture;
    }

    /**
     * @param string $culture
     * @return YanWenTracker
     */
    public function setCulture($culture)
    {
        $this->culture = $culture;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $parameters  = [
            'key' => $this->key,
            'culture' => $this->culture,
            'tracking_number' => $trackingNumber
        ];
        $request = new Request('GET', static::TRACKING_ENDPOINT);
        $json = $this->sendRequest($request, [
            'query' => $parameters
        ]);
        if ($json['code'] != 0) {
            throw new TrackException(sprintf('Bad response with code "%d"', $json['code']));
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
        if (is_null($json['origin_items']) && is_null($json['destin_items'])) {
            throw new TrackException(sprintf('Bad response'));
        }
        $shippingItems = array_merge($json['origin_items'], $json['destin_items']);
        $events = array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => $item['location'],
                'description' => $item['message'],
                'date' => Carbon::parse($item['timestamp']),
                'status' => null
            ]);
        }, $shippingItems);
        $shipment = new Shipment($events);

        //build origin events
        $shipment->setOriginEvents(array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => $item['location'],
                'description' => $item['message'],
                'date' => Carbon::parse($item['timestamp']),
                'status' => null
            ]);
        }, (array)$json['origin_items']));

        //build destination events
        $shipment->setDestinationEvents(array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => $item['location'],
                'description' => $item['message'],
                'date' => Carbon::parse($item['timestamp']),
                'status' => null
            ]);
        }, (array)$json['destin_items']));

        $shipment->setIsDelivered($json['state'] == 40)
            ->setOrigin($json['origin_country'])
            ->setDestination($json['destin_country'])
            ->setDeliveredAt($json['receive_date']);
        return $shipment;
    }
}