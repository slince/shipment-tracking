<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\SeventeenTrack;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;
use GuzzleHttp\Client as HttpClient;

class SeventeenTracker extends HttpAwareTracker
{
    const TRACK_ENDPOINT = 'http://www.17track.net/restapi/handlertrack.ashx';

    const REFERER = 'http://www.17track.net/zh-cn/track?nums={trackingNumber}';

    public function __construct(HttpClient $httpClient = null)
    {
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $request = static::createRequest($trackingNumber);
        $response = $this->sendRequest($request);
        $json = \GuzzleHttp\json_decode($response->getBody(), true);
        if ($json['ret'] != 1) {
            throw new TrackException($json['msg']);
        }
        return static::buildShipment($json);
    }

    /**
     * @param string $trackingNumber
     * @return Request
     */
    protected static function createRequest($trackingNumber)
    {
        $parameterKey = [
            'guid' => '',
            'data' => [
                [
                    'num' => $trackingNumber
                ]
            ]
        ];
        return new Request('POST', static::TRACK_ENDPOINT, [
                'Referer' => str_replace('{trackingNumber}', $trackingNumber, static::REFERER),
                'X-Requested-With' => 'XMLHttpRequest',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ],
            \GuzzleHttp\json_encode($parameterKey)
        );
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    protected function sendRequest(RequestInterface $request, $options = [])
    {
        return $this->getHttpClient()->send($request, $options);
    }

    /**
     * @param array $json
     * @return Shipment
     */
    protected static function buildShipment($json)
    {
        if (empty($json['dat'][0]['track'])) {
            throw new TrackException('Bad response');
        }
        $track = $json['dat'][0]['track'];
        $shipment = new Shipment();
        //源国家的跟踪轨迹
        $originEvents = array_map(function($item){
            return ShipmentEvent::fromArray([
                'description' => $item['z'],
                'location' => $item['d'] ?:  $item['c'],
                'date' => Carbon::parse($item['a'])
            ]);
        }, array_reverse($track['z1']));
        $shipment->setOriginEvents($originEvents);
        //目标国家跟踪轨迹
        $destinationEvents = array_map(function($item){
            return ShipmentEvent::fromArray([
                'description' => $item['z'],
                'location' => $item['d'] ?:  $item['c'],
                'date' => Carbon::parse($item['a'])
            ]);
        }, array_reverse($track['z2']));
        $shipment->setDestinationEvents($destinationEvents);
        $shipment->setEvents(array_merge($shipment->getOriginEvents(), $shipment->getDestinationEvents()));
        //状态
        $shipment->setStatus($track['e'])
            ->setIsDelivered($shipment->getStatus() === Shipment::STATUS_DELIVERED);
        //发货国家与目的国家暂时不得而知
        return $shipment;
    }
}