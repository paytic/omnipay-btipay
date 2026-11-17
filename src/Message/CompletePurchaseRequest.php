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
    protected function parseNotification(): array
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
        if (method_exists($response, 'getBindingInfo')) {
            $bindingInfo = $response->getBindingInfo();
            $data['bindingInfo'] = is_array($bindingInfo)
                ? $bindingInfo
                : $this->normalizeBindingInfo($bindingInfo);
        }

        $data['order'] = $data;
        return $data;
    }

    protected function normalizeBindingInfo($bindingInfo): array
    {
        if (!is_object($bindingInfo)) {
            return [];
        }

        $data = [];
        foreach ([
            'clientId',
            'bindingId',
            'cardArtUrl',
            'cardArtPicture',
            'cardArtForegroundColor',
            'bin',
            'panLastFour',
        ] as $field) {
            $getter = 'get' . ucfirst($field);
            if (method_exists($bindingInfo, $getter)) {
                $data[$field] = $bindingInfo->$getter();
            } elseif (isset($bindingInfo->$field)) {
                $data[$field] = $bindingInfo->$field;
            }
        }

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
