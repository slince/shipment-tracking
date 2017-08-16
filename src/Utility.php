<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking;

use Slince\ShipmentTracking\Foundation\Exception\InvalidArgumentException;

final class Utility
{
    /**
     * Parse the xml to an array
     * @param string $xml
     * @throws
     * @return array
     */
    public static function parseXml($xml)
    {
        libxml_use_internal_errors(true);
        $data = simplexml_load_string($xml, null, LIBXML_NOERROR);
        if ($data === false) {
            throw new InvalidArgumentException(sprintf('Invalid xml response "%s"', $xml));
        }
        return json_decode(json_encode($data), true);
    }
}