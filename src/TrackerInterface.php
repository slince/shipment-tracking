<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

use Slince\ShipmentTracking\Exception\TrackException;

interface TrackerInterface
{
    /**
     * Track the given number
     * @param string $trackingNumber
     * @throws TrackException
     * @return Shipment
     */
    public function track($trackingNumber);
}