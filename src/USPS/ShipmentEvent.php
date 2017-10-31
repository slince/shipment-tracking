<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\USPS;

use Slince\ShipmentTracking\Foundation\ShipmentEvent as BaseShipmentEvent;

class ShipmentEvent extends BaseShipmentEvent
{
    /**
     * @deprecated
     * @var string
     */
    protected $day;

    /**
     * @deprecated
     * @var string
     */
    protected $city;

    /**
     * @deprecated
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $zipCode;

    /**
     * @deprecated
     * @var string
     */
    protected $country;

    /**
     * @return string
     * @deprecated
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return ShipmentEvent
     * @deprecated
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return ShipmentEvent
     * @deprecated
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     * @return ShipmentEvent
     * @deprecated
     */
    public function setDay($day)
    {
        $this->day = $day;
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
     * @return ShipmentEvent
     * @deprecated
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     * @return ShipmentEvent
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }
}