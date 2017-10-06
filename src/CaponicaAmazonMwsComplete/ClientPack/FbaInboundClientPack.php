<?php

namespace CaponicaAmazonMwsComplete\ClientPack;

use CaponicaAmazonMwsComplete\AmazonClient\FbaInboundClient;
use CaponicaAmazonMwsComplete\ClientPool\MwsClientPoolConfig;
use CaponicaAmazonMwsComplete\Domain\Throttle\ThrottleAwareClientPackInterface;
use CaponicaAmazonMwsComplete\Domain\Throttle\ThrottledRequestManager;

class FbaInboundClientPack extends FbaInboundClient implements ThrottleAwareClientPackInterface {
    const PARAM_MARKETPLACE_ID                          = 'MarketplaceId';
    const PARAM_MERCHANT                                = 'SellerId';
    const PARAM_ITEMS                                   = 'Items';
    const PARAM_INCLUDE_COD_PREVIEW                     = 'IncludeCODFulfillmentPreview';
    const PARAM_INCLUDE_SCHEDULED_PREVIEW               = 'IncludeDeliveryWindows';
    const PARAM_DESTINATION_ADDRESS                     = 'DestinationAddress';
    const PARAM_DISPLAYABLE_ORDER_COMMENT               = 'DisplayableOrderComment';
    const PARAM_DISPLAYABLE_ORDER_DATETIME              = 'DisplayableOrderDateTime';
    const PARAM_DISPLAYABLE_ORDER_ID                    = 'DisplayableOrderId';
    const PARAM_NOTIFICATION_EMAIL_LIST                 = 'NotificationEmailList';
    const PARAM_SHIPPING_SPEED_CATEGORIES               = 'ShippingSpeedCategories';
    const PARAM_SHIPPING_SPEED_CATEGORY                 = 'ShippingSpeedCategory';
    const PARAM_SELLER_ORDER_ID                         = 'SellerFulfillmentOrderId';
    const PARAM_SHIPMENT_STATUS_LIST                    = 'ShipmentStatusList';
    const PARAM_SHIPMENT_ID_LIST                        = 'ShipmentIdList';
    const PARAM_LAST_UPDATED_BEFORE                     = 'LastUpdatedBefore';
    const PARAM_LAST_UPDATED_AFTER                      = 'LastUpdatedAfter';
    const PARAM_SHIPMENT_ID                             = 'ShipmentId';
    const SHIPPING_SPEED_STANDARD                       = 'Standard';
    const SHIPPING_SPEED_EXPEDITED                      = 'Expedited';
    const SHIPPING_SPEED_PRIORITY                       = 'Priority';
    const SHIPPING_SPEED_SCHEDULED                      = 'ScheduledDelivery';

    const METHOD_GET_SHIPMENTS_LIST                     = 'listInboundShipments';
    const METHOD_GET_SHIPMENT_ITEMS_LIST               = 'listInboundShipmentItems';


    /** @var string $marketplaceId      The MWS MarketplaceID string used in API connections */
    protected $marketplaceId;
    /** @var string $sellerId           The MWS SellerID string used in API connections */
    protected $sellerId;

    public function __construct(MwsClientPoolConfig $poolConfig) {
        $this->marketplaceId    = $poolConfig->getMarketplaceId();
        $this->sellerId         = $poolConfig->getSellerId();

        $this->initThrottleManager();

        parent::__construct(
            $poolConfig->getAccessKey(),
            $poolConfig->getSecretKey(),
            $poolConfig->getApplicationName(),
            $poolConfig->getApplicationVersion(),
            $poolConfig->getConfigForOrder($this->getServiceUrlSuffix())
        );
    }

    private function getServiceUrlSuffix() {
        return '/FulfillmentInboundShipment/' . self::SERVICE_VERSION;
    }

    // ##################################################
    // #      basic wrappers for API calls go here      #
    // ##################################################
    public function callGetlstInboundShipments($sellerFulfillmentOrderId) {
        $requestArray = [
            self::PARAM_MERCHANT            => $this->sellerId,
            self::PARAM_SELLER_ORDER_ID     => $sellerFulfillmentOrderId,
        ];

        return CaponicaClientPack::throttledCall($this, self::METHOD_GET_FULFILLMENT_ORDER, $requestArray);
    }

    /**
     * @param string $sellerOrderId
     * @param string $displayableOrderId
     * @param \DateTime $displayableOrderDatetime
     * @param string $displayableOrderComment
     * @param string $shippingSpeed
     * @param Address $destinationAddress
     * @param CreateFulfillmentOrderItem[] $items
     * @param null $notificationEmails
     * @return mixed
     */
    public function callGetlistInboundShipments( $shipmentStatusList, $shipmentIdList=null, $lastUpdatedBefore=null, $lastUpdatedAfter=null) {
        $requestArray = [
            self::PARAM_MERCHANT                    => $this->sellerId,
            self::PARAM_MARKETPLACE_ID              => $this->marketplaceId,
            self::PARAM_SHIPMENT_STATUS_LIST        => $shipmentStatusList,
            self::PARAM_SHIPMENT_ID_LIST            => $shipmentIdList,
            self::PARAM_LAST_UPDATED_BEFORE         => $lastUpdatedBefore,
            self::PARAM_LAST_UPDATED_AFTER          => $lastUpdatedAfter,
        ];



        return CaponicaClientPack::throttledCall($this, self::METHOD_GET_SHIPMENTS_LIST, $requestArray);
    }
    /**
     * @param string $sellerOrderId
     * @param string $displayableOrderId
     * @param \DateTime $displayableOrderDatetime
     * @param string $displayableOrderComment
     * @param string $shippingSpeed
     * @param Address $destinationAddress
     * @param CreateFulfillmentOrderItem[] $items
     * @param null $notificationEmails
     * @return mixed
     */
    public function callGetlistInboundShipmentItems($shipmentId, $lastUpdatedBefore=null, $lastUpdatedAfter=null) {
        $requestArray = [
            self::PARAM_MERCHANT                    => $this->sellerId,
            self::PARAM_MARKETPLACE_ID              => $this->marketplaceId,
            self::PARAM_SHIPMENT_ID                 => $shipmentId,
            self::PARAM_LAST_UPDATED_BEFORE         => $lastUpdatedBefore,
            self::PARAM_LAST_UPDATED_AFTER          => $lastUpdatedAfter,
        ];


        return CaponicaClientPack::throttledCall($this, self::METHOD_GET_SHIPMENT_ITEMS_LIST, $requestArray);
    }

    /**
     * @param Address $destinationAddress
     * @param CreateFulfillmentOrderItem[] $items
     * @param string $shippingSpeeds
     * @param boolean $includeCOD
     * @param boolean $includeScheduledDelivery
     * @return mixed
     */
    public function callGetFulfillmentPreview(Address $destinationAddress, $items
        , $shippingSpeeds=null, $includeCOD=null, $includeScheduledDelivery=null
    ) {
        $requestArray = [
            self::PARAM_MERCHANT                    => $this->sellerId,
            self::PARAM_MARKETPLACE_ID              => $this->marketplaceId,
            self::PARAM_DESTINATION_ADDRESS         => $destinationAddress->getArray(),
        ];

        $itemList = [];
        foreach ($items as $item) {
            $itemList[] = $item->getArray();
        }
        $itemListWithMemberKey = ['member'];
        $itemListWithMemberKey['member'] = $itemList;
        $requestArray[self::PARAM_ITEMS] = $itemListWithMemberKey;

        if (!empty($shippingSpeeds)) {
            $requestArray[self::PARAM_SHIPPING_SPEED_CATEGORIES] = $shippingSpeeds;
        }
        if (isset($includeCOD)) {
            $requestArray[self::PARAM_INCLUDE_COD_PREVIEW] = $includeCOD;
        }
        if (isset($includeScheduledDelivery)) {
            $requestArray[self::PARAM_INCLUDE_SCHEDULED_PREVIEW] = $includeScheduledDelivery;
        }

        return CaponicaClientPack::throttledCall($this, self::METHOD_GET_FULFILLMENT_PREVIEW, $requestArray);
    }

    // ###################################################
    // # ThrottleAwareClientPackInterface implementation #
    // ###################################################
    private $throttleManager;

    public function initThrottleManager() {
        $this->throttleManager = new ThrottledRequestManager(
            [
                self::METHOD_GET_SHIPMENTS_LIST                    => [30, 2],
                self::METHOD_GET_SHIPMENT_ITEMS_LIST               => [30, 2],
            ]
        );
    }

    public function getThrottleManager() {
        return $this->throttleManager;
    }
}
