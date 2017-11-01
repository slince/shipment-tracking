<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */

namespace Slince\ShipmentTracking\Common\Location;

use Rinvex\Country\Country;
use Slince\ShipmentTracking\Foundation\Location\AbstractLocation;
use Exception;

class Location extends AbstractLocation
{
    /**
     * @var string
     */
    protected $countryName;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var Country
     */
    protected $country;

    public function __construct($countryCode, $state, $city)
    {
        $countryCode = strtolower($countryCode);
        $countryCode && $this->applyCountry($countryCode);
        $this->state = $state;
        $this->city = $city;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return trim(implode(' ', [$this->city, $this->state, $this->countryName]));
    }

    protected function applyCountry($countryCode)
    {
        try {
            $this->country = country($countryCode);
            $this->countryName = $this->country->getName();
        } catch (Exception $exception) {
            $this->countryName = $countryCode;
        }
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     * @return Location
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Location
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Location
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }
}