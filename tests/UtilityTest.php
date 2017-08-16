<?php
namespace Slince\ShipmentTracking\Tests;

use PHPUnit\Framework\TestCase;
use Slince\ShipmentTracking\Foundation\Exception\InvalidArgumentException;
use Slince\ShipmentTracking\Utility;

class UtilityTest extends TestCase
{
    public function testParseXMl()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Error>
    <Number>80040B1A</Number>
    <Description>Authorization failure.  Perhaps username and/or password is incorrect.</Description>
    <Source>USPSCOM::DoAuth</Source>
</Error>
XML;
        $this->assertEquals([
            'Number' => '80040B1A',
            'Description' => 'Authorization failure.  Perhaps username and/or password is incorrect.',
            'Source' => 'USPSCOM::DoAuth'
        ], Utility::parseXml($xml));

        $invalidXml = <<<XML
<?xml?>
<Error>
    <Nr>80040B1A</Nr>
    <Description>Authorization failure.  Perhaps username and/or password is incorrect.</Description>
    <Source>USPSCOM::DoAuth</Source>
</Error>
XML;
        $this->expectException(InvalidArgumentException::class);
        Utility::parseXml($invalidXml);
    }
}