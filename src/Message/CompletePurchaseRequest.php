<?php

namespace Paytic\Omnipay\Btipay\Message;

use Paytic\Omnipay\Common\Message\Traits\GatewayNotificationRequestTrait;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class CompletePurchaseRequest extends AbstractRequest
{
    use GatewayNotificationRequestTrait;

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = [];
        $data['orderId'] = $this->httpRequest->query->get('orderId');

        return $data;
    }

    /**
     * @return mixed
     */
    public function isValidNotification()
    {
        return $this->hasGET('orderId');
    }
}
