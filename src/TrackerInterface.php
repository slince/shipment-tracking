<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

interface TrackerInterface
{
    /**
     * Track the given number
     * @param string $trackingNumber
     * @return Shipment
     */
    public function track($trackingNumber);
}