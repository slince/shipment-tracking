<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\USPS;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use GuzzleHttp\Client as HttpClient;
use Slince\ShipmentTracking\Foundation\Shipment;
use Slince\ShipmentTracking\Utility;

class USPSTracker extends HttpAwareTracker
{
    const TRACKING_ENDPOINT  = 'http://production.shippingapis.com/ShippingAPI.dll';

    /**
     * @var string
     */
    protected $userId;

    /**
     * You can get your userID from the following url
     * {@link https://www.usps.com/business/web-tools-apis/welcome.htm}
     */
    public function __construct($userId, HttpClient $httpClient = null)
    {
        $this->userId = $userId;
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     * @return USPSTracker
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        try {
            $response = $this->request([
                'query' => [
                    'API' => 'TrackV2',
                    'XML' => static::buildXml($this->userId, $trackingNumber)
                ]
            ]);
            $array = Utility::parseXml($response->getBody());
        } catch (\Exception $exception) {
            throw new TrackException($exception->getMessage());
        }
        if (!isset($array['TrackInfo'])) {
            throw new TrackException($array['Description']);
        }
        if (isset($array['TrackInfo']['Error'])) {
            throw new TrackException($array['TrackInfo']['Error']['Description']);
        }
        if (!isset($array['TrackInfo']['TrackSummary']) && !isset($array['TrackInfo']['TrackDetail'])) {
            throw new TrackException('Cannot find any events');
        }
        $shipment = static::buildShipment($array);
        return $shipment;
    }

    /**
     * @param array $options
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    protected function request($options)
    {
        return $this->getHttpClient()->get(static::TRACKING_ENDPOINT, $options);
    }

    /**
     * @param string $userId
     * @param string $trackID
     * @return string
     */
    protected static function buildXml($userId, $trackID)
    {
        $xmlTemplate = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<TrackFieldRequest USERID="%s">
  <ClientIp>111.0.0.1</ClientIp>
  <TrackID ID="%s"></TrackID>
</TrackFieldRequest>
XML;
        return sprintf($xmlTemplate, $userId, $trackID);
    }

    /**
     * @param array $array
     * @return Shipment
     */
    protected static function buildShipment($array)
    {
        if (!empty($array['TrackInfo']['TrackDetail'])) {
            $trackDetails = is_numeric(key($array['TrackInfo']['TrackDetail']))
                ? $array['TrackInfo']['TrackDetail']
                : [$array['TrackInfo']['TrackDetail']];
        } else {
            $trackDetails =  [];
        }
        //The track summary is also valid
        if (isset($array['TrackInfo']['TrackSummary'])) {
            array_unshift($trackDetails, $array['TrackInfo']['TrackSummary']);
        }
        $events = array_map(function($eventData){
            $time = empty($eventData['EventTime']) ? '' : $eventData['EventTime'];
            $day = empty($eventData['EventDate']) ? '' : $eventData['EventDate'];
            $country = empty($eventData['EventCountry']) ? '' : $eventData['EventCountry'];
            $state = empty($eventData['EventState']) ? '' : $eventData['EventState'];
            $city = empty($eventData['EventCity']) ? '' : $eventData['EventCity'];
            $zipCode = empty($eventData['EventZIPCode']) ? '' : $eventData['EventZIPCode'];
            return ShipmentEvent::fromArray([
                'day' => $day,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'date' => Carbon::parse("{$day} {$time}"),
                'location' => new Location($country, $state, $city),
                'description' => $eventData['Event'],
                'zipCode' => $zipCode
            ]);
        }, array_reverse($trackDetails));
        
        $shipment = new Shipment($events);
        if (isset($array['TrackInfo']['TrackSummary']['DeliveryAttributeCode'])) {
            $shipment->setIsDelivered($array['TrackInfo']['TrackSummary']['DeliveryAttributeCode'] == '01');
        }
        if ($firstEvent = reset($events)) {
            $shipment->setDeliveredAt($firstEvent->getDate());
        }
        return $shipment;
    }
}