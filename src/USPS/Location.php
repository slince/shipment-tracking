<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */

namespace Slince\ShipmentTracking\USPS;

use Slince\ShipmentTracking\Common\Location\Location as BaseLocation;

class Location  extends BaseLocation
{
    public function __construct($countryName, $state, $city)
    {
        $this->countryName = $countryName;
        $this->state = $state;
        $this->city = $city;
    }
}