<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracker;

interface ShipmentInterface
{
    /**
     * @return ShipmentEvent[]
     */
    public function getEvents();
}