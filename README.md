# Shipment Tracking SDK 

[![Build Status](https://img.shields.io/travis/slince/shipment-tracking/master.svg?style=flat-square)](https://travis-ci.org/slince/shipment-tracking)
[![Coverage Status](https://img.shields.io/codecov/c/github/slince/shipment-tracking.svg?style=flat-square)](https://codecov.io/github/slince/shipment-tracking)
[![Latest Stable Version](https://img.shields.io/packagist/v/slince/shipment-tracking.svg?style=flat-square&label=stable)](https://packagist.org/packages/slince/shipment-tracking)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/slince/shipment-tracking.svg?style=flat-square)](https://scrutinizer-ci.com/g/slince/shipment-tracking/?branch=master)

A flexible and awesome shipment tracking library for several carriers like DHL eCommerce, YanWen Express, Epacket, E包裹, E特快, 国际EMS, 快递100

## Installation

Install via composer

```bash
$ composer require slince/shipment-tracking
```

## Table of Contents

- [DHL eCommerce](#dhl-ecommerce)
- [YanWen Express(燕文物流)](#yanwen-express燕文物流)
- [E邮宝(Epacket、EUP)、E包裹、E特快、国际EMS](#中国邮政)
- [快递100](#快递100)
- [USPS](#usps)
- [递四方](#递四方)

## Basic Usage

### DHL eCommerce

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
   
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```
The above code will get access token automatically for shipment information.

#### Access Token

```php
$shipment = $tacker->track('CNAQV100168101);
$accessToken = $tracker->getAccessToken(); //You can save this for the next query

//... to do

try{
    $tracker->setAccessToken($accessToken); //Set the access token; the tracker will not send requst for the access token
    $shipment = $tacker->track('CNAQV100168101);
} catch (Slince\ShipmentTracking\DHLECommerce\Exception\InvalidAccessTokenException $exception) {
     $accessToken = $tracker->getAccessToken(true); // If the access token is invalid, refresh it.
     $shipment = $tacker->track('CNAQV100168101);
     //... to do
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}
```

### YanWen Express(燕文物流)

```php

$tracker = new Slince\ShipmentTracking\YanWenExpress\YanWenTracker(KEY, 'en');

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   if ($shipment->isDelivered()) {
       echo "Delivered";
   }
   echo $shipment->getOrigin();
   echo $shipment->getDestination();
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```

### 中国邮政 

适用中邮旗下E邮宝(Epacket、EUP)、E包裹、E特快、国际EMS产品


```php

$tracker = new Slince\ShipmentTracking\EMS\EMSTracker(AUTHENTICATE, 'en');

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```

### 快递100

```php

$tracker = new Slince\ShipmentTracking\KuaiDi100\KuaiDi100Tracker(APPKEY, 'shunfeng'); //承运商名称并不是标准的承运商代码，实际承运商代码请到kuaidi100.com查看

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   if ($shipment->isDelivered()) {
       echo "Delivered";
   }
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```
快递100的key需要自行申请，免费版的key在查询申通顺丰之类的单号时会受限，需要企业版才可以；附上快递100[文档](https://www.kuaidi100.com/openapi/api_post.shtml)

### USPS

```php

$tracker = new Slince\ShipmentTracking\USPS\USPSTracker(USER_ID);

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   if ($shipment->isDelivered()) {
       echo "Delivered";
   }
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```

You can get your user id on the following url.

[https://www.usps.com/business/web-tools-apis/welcome.htm](https://www.usps.com/business/web-tools-apis/welcome.htm)

### 递四方

```php

$tracker = new Slince\ShipmentTracking\FourPartyExpress\FourPartyExpressTracker(APPKEY, APPSECRET);

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Foundation\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```

APPKEY和APPSECRET 需要到递四方官网注册APP,审核之后即可获取到该参数；

## License
 
The MIT license. See [MIT](https://opensource.org/licenses/MIT)

