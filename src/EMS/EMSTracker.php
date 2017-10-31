<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\EMS;

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Slince\ShipmentTracking\Foundation\Exception\InvalidArgumentException;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use Slince\ShipmentTracking\Foundation\Shipment;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;
use Slince\ShipmentTracking\Utility;

class EMSTracker extends HttpAwareTracker
{
    /**
     * @var string
     */
    const TRACKING_ENDPOINT = 'http://shipping.ems.com.cn/partner/api/public/p/track/query/{language}/{trackingNumber}';

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected static $version = 'international_eub_us_1.1';

    /**
     * @var string
     */
    protected $authenticate;

    public function __construct($authenticate, $language, HttpClient $httpClient = null)
    {
        $this->authenticate = $authenticate;
        $this->setLanguage($language);
        $httpClient && $this->setHttpClient($httpClient);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return EMSTracker
     */
    public function setLanguage($language)
    {
        if ($language != 'en' && $language != 'cn') {
            throw new InvalidArgumentException(sprintf('Invalid language, expect "cn" or "en", giving "%s"', $language));
        }
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticate()
    {
        return $this->authenticate;
    }

    /**
     * @param string $authenticate
     * @return EMSTracker
     */
    public function setAuthenticate($authenticate)
    {
        $this->authenticate = $authenticate;
        return $this;
    }

    /**
     * @param string $version
     */
    public static function setVersion($version)
    {
        static::$version = $version;
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return static::$version;
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $request = new Request('GET', static::formatEndpoint($this->language, $trackingNumber));
        try {
            $array = Utility::parseXml($this->sendRequest($request));
        } catch (InvalidArgumentException $exception) {
            throw new TrackException($exception->getMessage());
        }
        if (empty($array['trace'])) {
            throw new TrackException(sprintf('Bad response,code: "%s", message:"%s"', $array['code'], $array['description']));
        }
        return static::buildShipment($array);
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return string
     * @codeCoverageIgnore
     */
    protected function sendRequest(RequestInterface $request, array $options = [])
    {
        try {
            $request = $request->withHeader('version', static::$version)
                ->withHeader('authenticate', $this->authenticate);
            $response = $this->getHttpClient()->send($request, $options);
            return (string)$response->getBody();
        } catch (GuzzleException $exception) {
            throw new TrackException($exception->getMessage());
        }
    }

    /**
     * @param string $language
     * @param string $trackingNumber
     * @return string
     */
    protected static function formatEndpoint($language, $trackingNumber)
    {
        return str_replace(['{language}', '{trackingNumber}'], [$language, $trackingNumber], static::TRACKING_ENDPOINT);
    }

    /**
     * @param array $json
     * @return Shipment
     */
    protected static function buildShipment($json)
    {
        $trace = is_numeric(key($json['trace'])) ? $json['trace'] : [$json['trace']];
        $events = array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => $item['acceptAddress'],
                'description' => $item['remark'],
                'date' => Carbon::parse($item['acceptTime']),
            ]);
        }, $trace);
        $shipment = new Shipment($events);
        if ($firstEvent = reset($events)) {
            $shipment->setDeliveredAt($firstEvent->getDate());
        }
        return $shipment;
    }
}