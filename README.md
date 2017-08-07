# Shipment Tracking Library

[![Build Status](https://img.shields.io/travis/slince/shipment-tracking/master.svg?style=flat-square)](https://travis-ci.org/slince/shipment-tracking)
[![Coverage Status](https://img.shields.io/codecov/c/github/slince/shipment-tracking.svg?style=flat-square)](https://codecov.io/github/slince/shipment-tracking)
[![Latest Stable Version](https://img.shields.io/packagist/v/slince/shipment-tracking.svg?style=flat-square&label=stable)](https://packagist.org/packages/slince/shipment-tracking)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/slince/shipment-tracking.svg?style=flat-square)](https://scrutinizer-ci.com/g/slince/shipment-tracking/?branch=master)

A flexible and shipment tracking library for multi carriers.

## Basic Usage

```php

$tracker = new Slince\ShipmentTracking\DHLECommerce\DHLECommerceTracker(CLIENT_ID, PASSWORD);

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   if ($shipment->isDelivered()) {
       echo "Delivered";
   }
   echo $shipment->getOrigin();
   echo $shipment->getDestination();
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```

All shipment trackers must implement `Slince\ShipmentTracking\TrackerInterface`, and will usually extend `Slince\ShipmentTracking\HttpAwareTracker` for basic functionality if the carrier's api is based on
HTTP

## Shipment trackers:

The following carriers are available:

| Tracker | Composer Package | Maintainer |
| --- | --- | --- |
| [DHL eCommerce](https://github.com/slince/shipment-tracking-dhl-ecommerce)| slince/shipment-tracking-dhl-ecommerce | [Tao](https://github.com/slince) |
| [Yanwen Exprerss(燕文物流)](https://github.com/slince/shipment-tracking-yanwen-express)| slince/shipment-tracking-yanwen-express | [Tao](https://github.com/slince) |
| [快递100](https://github.com/slince/shipment-tracking-kuaidi100)| slince/shipment-tracking-kuaidi100 | [Tao](https://github.com/slince) |
| [E邮宝/E包裹/特快/国际EMS](https://github.com/slince/shipment-tracking-ems)| slince/shipment-tracking-ems | [Tao](https://github.com/slince) |

## License
 
The MIT license. See [MIT](https://opensource.org/licenses/MIT)

