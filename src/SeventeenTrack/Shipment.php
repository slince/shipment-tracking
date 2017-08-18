<?php
/**
 * Slince shipment tracker library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\ShipmentTracking\SeventeenTrack;

use Slince\ShipmentTracking\YanWenExpress\Shipment as BaseShipment;

class Shipment extends BaseShipment
{
    /**
     * 投递成功
     */
    const STATUS_DELIVERED = 40;

    /**
     * 运输中
     */
    const STATUS_DELIVERING = 10;

    /**
     * 运输过久
     */
    const STATUS_TRANSPORT_TOO_LONG = 20;

    /**
     * 已到达投递点，等待领取
     */
    const STATUS_WAITING_TO_RECEIVE = 30;

    /**
     * 投递失败
     */
    const STATUS_DELIVERY_FAILED =  35;

    /**
     * 可能异常
     */
    const STATUS_MAY_ABNORMAL = 50;
}