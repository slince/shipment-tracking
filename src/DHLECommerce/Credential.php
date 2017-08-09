<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\DHLECommerce;

class Credential
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $password;

    public function __construct($clientId, $password)
    {
        $this->clientId = $clientId;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     * @return Credential
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return Credential
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Converts the credential to an array
     * @return array
     */
    public function toArray()
    {
        return [
            'clientId' => $this->clientId,
            'password' => $this->password
        ];
    }
}