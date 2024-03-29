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

        $btClient = $this->getClient();
        $response = $btClient->getOrderStatusExtendedByOrderId($data['orderId']);

        $data['orderNumber'] = $response->getOrderNumber();
        $data['status'] = $response->getOrderStatus();
        $data['actionCode'] = $response->getActionCode();
        $data['actionCodeDescription'] = $response->getActionCodeDescription();
        $data['amount'] = $response->getAmount();
        $data['currency'] = $response->getCurrency();
        $data['orderDescription'] = $response->getOrderDescription();
        $cardAuthInfo = $response->getCardAuthInfo();
        $data['card_name'] = $cardAuthInfo->getCardholderName();
        $data['card_number'] = $cardAuthInfo->getPan();
        $data['card_exp'] = $cardAuthInfo->getExpiration();
        return $data;
    }

    /**
     * @return mixed
     */
    public function isValidNotification()
    {
        return
            $this->hasGET('orderId')
            && $this->hasGET('token')
            && $this->hasGET('approvalCode');
    }
}
