<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

interface ShipmentInterface
{
    /**
     * @return ShipmentEvent[]
     */
    public function getEvents();
}