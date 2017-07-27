# Shipment Tracker

[![Build Status](https://img.shields.io/travis/slince/shipment-tracking/master.svg?style=flat-square)](https://travis-ci.org/slince/shipment-tracking)
[![Coverage Status](https://img.shields.io/codecov/c/github/slince/shipment-tracking.svg?style=flat-square)](https://codecov.io/github/slince/shipment-tracking)
[![Total Downloads](https://img.shields.io/packagist/dt/slince/shipment-tracking.svg?style=flat-square)](https://packagist.org/packages/slince/shipment-tracking)
[![Latest Stable Version](https://img.shields.io/packagist/v/slince/shipment-tracking.svg?style=flat-square&label=stable)](https://packagist.org/packages/slince/shipment-tracking)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/slince/shipment-tracking.svg?style=flat-square)](https://scrutinizer-ci.com/g/slince/shipment-tracking/?branch=master)

A flexible and shipment tracking library for multi carriers.

## Basic Usage

```php

$tracker = new Slince\ShipmentTracking\DHLECommerce\DHLECommerceTracker();

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   if ($shipment->isDelivered()) {
       echo "Delivered";
   }
   
   //print the shipment events
   print_r($shipment->getEvents());
}

```

All payment gateways must implement GatewayInterface, and will usually extend AbstractGateway for basic functionality.

## Shipment Carriers:

The following carriers are available:

| Carrier | Composer Package | Maintainer |
| --- | --- | --- |
| [https://github.com/slince/shipment-tracking-dhlecommerce](DHL eCommerce)| slince/shipment-tracking-dhlecommerce | Tao |

## License
 
The MIT license. See [MIT](https://opensource.org/licenses/MIT)

