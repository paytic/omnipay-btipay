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

        $order = [];
        $order['orderNumber'] = $response->getOrderNumber();
        $order['status'] =  $data['status'] = $response->getOrderStatus();
        $order['actionCode'] = $response->getActionCode();
        $order['actionCodeDescription'] = $response->getActionCodeDescription();
        $order['amount'] = $response->getAmount();
        $order['currency'] = $response->getCurrency();
        $order['orderDescription'] = $response->getOrderDescription();
        $cardAuthInfo = $response->getCardAuthInfo();
        $order['card_name'] = $cardAuthInfo->getCardholderName();
        $order['card_number'] = $cardAuthInfo->getPan();
        $order['card_exp'] = $cardAuthInfo->getExpiration();

        $data['order'] = $order;
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
