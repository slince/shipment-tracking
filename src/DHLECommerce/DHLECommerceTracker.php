<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\DHLECommerce;

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Slince\ShipmentTracking\DHLECommerce\Exception\InvalidAccessTokenException;
use Slince\ShipmentTracking\Common\Location\Location;
use Slince\ShipmentTracking\Foundation\Shipment;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;
use Slince\ShipmentTracking\Foundation\HttpAwareTracker;
use Slince\ShipmentTracking\Foundation\Exception\TrackException;

class DHLECommerceTracker extends HttpAwareTracker
{
    /**
     * @var string
     */
    const ACCESS_TOKEN_ENDPOINT = 'https://api.dhlecommerce.dhl.com/rest/v1/OAuth/AccessToken';

    /**
     * @var string
     */
    const TRACKING_ENDPOINT = 'https://api.dhlecommerce.dhl.com/rest/v2/Tracking';

    /**
     * @var Credential
     */
    protected $credential;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    public function __construct($clientId, $password, HttpClient $httpClient = null)
    {
        $httpClient && $this->setHttpClient($httpClient);
        $this->credential = new Credential($clientId, $password);
    }

    /**
     * Sets the access token for the tracker
     * @param string|AccessToken $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken instanceof AccessToken ? $accessToken : new AccessToken($accessToken);
    }

    /**
     * {@inheritdoc}
     */
    public function track($trackingNumber)
    {
        $parameters = [
            'trackItemRequest' => [
                'token' => $this->getAccessToken()->getToken(),
                'messageLanguage' => 'en',
                'messageVersion' => '1.1',
                'trackingReferenceNumber' => [$trackingNumber]
            ]
        ];
        $request = new Request('POST', static::TRACKING_ENDPOINT);
        $json = $this->sendRequest($request, [
            'json' => $parameters
        ]);
        if ($json['trackItemResponse']['responseCode'] != 0 &&
            stripos($json['trackItemResponse']['responseText'], 'Invalid access token') !== false
        ) {
            throw new InvalidAccessTokenException(sprintf('The access token "%s" is invalid, please refresh for the new one', $this->accessToken));
        }
        if ($json['trackItemResponse']['responseCode'] != 0) {
            throw new TrackException(sprintf('Bad response with code "%d"', $json['trackItemResponse']['responseCode']));
        }
        return static::buildShipment($json);

    }

    /**
     * Gets the access token
     * @param boolean $refresh
     * @throws TrackException
     * @return AccessToken
     */
    public function getAccessToken($refresh = false)
    {
        if ($this->accessToken && !$refresh) {
            return $this->accessToken;
        }
        $request = new Request('GET', static::ACCESS_TOKEN_ENDPOINT);
        $json = $this->sendRequest($request, [
            'query' => array_merge($this->credential->toArray(), [
                'returnFormat' => 'json'
            ])
        ]);
        if (!isset($json['accessTokenResponse']['responseStatus']) || $json['accessTokenResponse']['responseStatus']['code'] != '100000') {
            throw new TrackException(sprintf('Get access token error with message details "%s"',
                $json['accessTokenResponse']['responseStatus']['messageDetails']
            ));
        }
        return $this->accessToken = new AccessToken(
            $json['accessTokenResponse']['token'],
            $json['accessTokenResponse']['token_type'],
            $json['accessTokenResponse']['expires_in_seconds']
        );
    }

    /**
     * @return Credential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @throws TrackException
     * @return array
     * @codeCoverageIgnore
     */
    protected function sendRequest(RequestInterface $request, $options = [])
    {
        try {
            $response = $this->getHttpClient()->send($request, $options);
        } catch (GuzzleException $exception) {
            throw new TrackException($exception->getMessage());
        }
        return \GuzzleHttp\json_decode((string)$response->getBody(), true);
    }

    /**
     * @param array $json
     * @throws TrackException
     * @return Shipment
     */
    protected static function buildShipment($json)
    {
        $json = reset($json['trackItemResponse']['items']);
        if (!$json) {
            throw new TrackException(sprintf('Bad response'));
        }
        $events = array_map(function($item) {
            return ShipmentEvent::fromArray([
                'location' => new Location($item['address']['countryCode'], $item['address']['state'], $item['address']['city']),
                'description' => $item['description'],
                'date' => Carbon::parse($item['timestamp']),
                'status' => $item['status']
            ]);
        }, array_reverse($json['events']));
        $shipment = new Shipment($events);
        $isDelivered = ($lastEvent = end($events)) ? $lastEvent->getStatus() == 71093 : null;

        $shipment->setIsDelivered($isDelivered);

        empty($json['destination']['countryCode']) || static::applyDestination($shipment, $json['destination']['countryCode']);

        if ($firstEvent = reset($events)) {
            $shipment->setDeliveredAt($firstEvent->getDate());
        }
        return $shipment;
    }

    protected static function applyDestination(Shipment $shipment, $countryCode)
    {
        $destination  = new Location($countryCode, null, null);
        $shipment->setDestination($destination);
    }
}