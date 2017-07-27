<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

interface ShipmentInterface
{
    /**
     * Gets the tracking items
     * @return ShipmentEvent[]
     */
    public function getEvents();

    /**
     * Checks whether the shipment is delivered
     * @return boolean
     */
    public function isDelivered();
}