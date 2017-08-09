<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\DHLECommerce;

final class AccessToken
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    protected $expiresIn;

    /**
     * @var string
     */
    protected $type;

    public function __construct($token, $type = null, $expiresIn = null)
    {
        $this->token = $token;
        $this->type = $type;
        $this->expiresIn = $expiresIn;
    }

    public function __toString()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return AccessToken
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     * @return AccessToken
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AccessToken
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}