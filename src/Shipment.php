<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracker;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Shipment
{
    /**
     * @var ShipmentEvent[]
     */
    protected $events;

    /**
     * @var float
     */
    protected $weight;

    /**
     * @var string
     */
    protected $weightUnit;

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var \DateTime
     */
    protected $deliveryDate;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     * @return Shipment
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return string
     */
    public function getWeightUnit()
    {
        return $this->weightUnit;
    }

    /**
     * @param string $weightUnit
     * @return Shipment
     */
    public function setWeightUnit($weightUnit)
    {
        $this->weightUnit = $weightUnit;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return Shipment
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     * @return Shipment
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     * @return Shipment
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    /**
     * @return ShipmentEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }
}
