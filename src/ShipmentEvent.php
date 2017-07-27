<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

class ShipmentEvent implements \JsonSerializable
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $status;

    public function __construct(\DateTime $date = null, $description = null, $location = null)
    {
        $this->date = $date;
        $this->description = $description;
        $this->location = $location;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return ShipmentEvent
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ShipmentEvent
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @return ShipmentEvent
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return ShipmentEvent
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Creates an event from an array
     * @param array $data
     * @return static
     */
    public static function fromArray($data)
    {
        $event = new static();
        foreach ($data as $property => $value) {
            if (method_exists($event, $method = 'set' . ucfirst($property))) {
                $event->$method($value);
            }
        }
        return $event;
    }

    /**
     * Converts the event to array
     * @return array
     */
    public function toArray()
    {
        $methods = get_class_methods($this);
        $data = [];
        foreach ($methods as $method) {
            if (substr($method, 0, 3) == 'get') {
                $property = lcfirst(substr($method, 3));
                $data[$property] = $this->$method();
            } elseif (substr($method, 0, 2) == 'is') {
                $property = lcfirst(substr($method, 2));
                $data[$property] = $this->$method();
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
