<?php

/**
 * Part of the caponica/amazon-mws-complete package
 *
 * The MwsClientPool is configured once for a given marketplace/seller/app and can then be used to retrieve
 * Amazon Clients for any of the MWS Service URLs.
 */

namespace CaponicaAmazonMwsComplete\ClientPool;

use CaponicaAmazonMwsComplete\ClientPack\FbaInboundClientPack;
use CaponicaAmazonMwsComplete\ClientPack\FbaOutboundClientPack;
use CaponicaAmazonMwsComplete\ClientPack\MwsFeedAndReportClientPack;
use CaponicaAmazonMwsComplete\ClientPack\MwsFinanceClientPack;
use CaponicaAmazonMwsComplete\ClientPack\MwsOrderClientPack;
use CaponicaAmazonMwsComplete\ClientPack\MwsProductClientPack;

class MwsClientPool {
    // $channelId can be used to stash an id that your code uses to reference this Client Pool's Amazon site
    protected $channelId;

    /**
     * @var MwsFeedAndReportClientPack
     */
    protected $feedAndReportClientPack;
    /**
     * @var MwsFinanceClientPack
     */
    protected $financeClientPack;
    /**
     * @var MwsOrderClientPack
     */
    protected $orderClientPack;
    /**
     * @var FbaOutboundClientPack
     */
    protected $fbaOutboundClientPack;
    /**
     * @var FbaInboundClientPack
     */
    protected $fbaInboundClientPack;
    /**
     * @var MwsProductClientPack
     */
    protected $productClientPack;

    /** @var MwsClientPoolConfig */
    protected $config;

    public function setConfig($config=[], $siteCode=null) {
        $this->config = new MwsClientPoolConfig($config, $siteCode);
    }

    /**
     * @return FbaOutboundClientPack
     */
    public function getFbaOutboundClientPack() {
        if(empty($this->fbaOutboundClientPack)) {
            $this->fbaOutboundClientPack = new FbaOutboundClientPack($this->config);
        }
        return $this->fbaOutboundClientPack;
    }

    /**
     * @return FbaInboundClientPack
     */
    public function getFbaInboundClientPack() {
        if(empty($this->fbaInboundClientPack)) {

            $this->fbaInboundClientPack = new FbaInboundClientPack($this->config);
        }
        return $this->fbaInboundClientPack;
    }

    /**
     * @return MwsFinanceClientPack
     */
    public function getFinanceClientPack() {
        if(empty($this->financeClientPack)) {
            $this->financeClientPack = new MwsFinanceClientPack($this->config);
        }
        return $this->financeClientPack;
    }

    /**
     * @return MwsOrderClientPack
     */
    public function getOrderClientPack() {
        if(empty($this->orderClientPack)) {
            $this->orderClientPack = new MwsOrderClientPack($this->config);
        }
        return $this->orderClientPack;
    }

    /**
     * @return MwsProductClientPack
     */
    public function getProductClientPack() {
        if(empty($this->productClientPack)) {
            $this->productClientPack = new MwsProductClientPack($this->config);
        }
        return $this->productClientPack;
    }

    /**
     * @return MwsFeedAndReportClientPack
     */
    public function getFeedAndReportClientPack() {
        if(empty($this->feedAndReportClientPack)) {
            $this->feedAndReportClientPack = new MwsFeedAndReportClientPack($this->config);
        }
        return $this->feedAndReportClientPack;
    }

    public function setChannelId($value) {
        return $this->channelId = $value;
    }
    public function getChannelId() {
        return $this->channelId;
    }
    public function getMarketplaceId() {
        return $this->config->getMarketplaceId();
    }
    public function getSellerId() {
        return $this->config->getSellerId();
    }
}
