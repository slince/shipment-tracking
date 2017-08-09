<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\YanWenExpress;

use Slince\ShipmentTracking\Foundation\Shipment as BaseShipment;
use Slince\ShipmentTracking\Foundation\ShipmentEvent;

class Shipment extends BaseShipment
{
    /**
     * @var ShipmentEvent[]
     */
    protected $originEvents;

    /**
     * @var ShipmentEvent[]
     */
    protected $destinationEvents;

    /**
     * @return ShipmentEvent[]
     */
    public function getDestinationEvents()
    {
        return $this->destinationEvents;
    }

    /**
     * @param ShipmentEvent[] $destinationEvents
     * @return Shipment
     */
    public function setDestinationEvents($destinationEvents)
    {
        $this->destinationEvents = $destinationEvents;
        return $this;
    }

    /**
     * @return ShipmentEvent[]
     */
    public function getOriginEvents()
    {
        return $this->originEvents;
    }

    /**
     * @param ShipmentEvent[] $originEvents
     * @return Shipment
     */
    public function setOriginEvents($originEvents)
    {
        $this->originEvents = $originEvents;
        return $this;
    }
}