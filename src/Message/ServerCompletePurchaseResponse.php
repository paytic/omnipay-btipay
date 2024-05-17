<?php

namespace Paytic\Omnipay\Btipay\Message;

use Paytic\Omnipay\Btipay\Utils\OrderStatus;
use Paytic\Omnipay\Common\Message\Traits\GatewayNotificationResponseTrait;
use Paytic\Omnipay\Common\Message\Traits\HasTokenTrait;
use Paytic\Omnipay\Common\Models\Token;
use DateTime;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseResponse extends AbstractResponse
{
    use GatewayNotificationResponseTrait;
    use HasTokenTrait;

    public function isSuccessful(): bool
    {
        return OrderStatus::isSuccessful($this->getTransactionStatus());
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isPending(): bool
    {
        return OrderStatus::isPending($this->getTransactionStatus());
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled(): bool
    {
        return OrderStatus::isCancelled($this->getTransactionStatus());
    }

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage(): ?string
    {
        return data_get($this->getData(), 'notification.actionCodeDescription');
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode(): ?string
    {
        return data_get($this->getData(), 'notification.actionCode');
    }

    /**
     * Gateway Reference
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return data_get($this->getData(), 'notification.orderId');
    }

    /**
     * Get the transaction ID as generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return data_get($this->getData(), 'notification.orderNumber');
    }

    public function getSessionDebug(): array
    {
        return [
            'notification' => $this->getDataProperty('notification'),
        ];
    }

    protected function getTransactionStatus()
    {
        return data_get($this->getData(), 'notification.status');
    }

    /**
     * @return Notify
     */
    public function getBtipayNotify()
    {
        return $this->getDataProperty('notification');
    }

    public function send()
    {
        header('Content-type: application/xml');
        echo $this->getContent();
    }

    public function getToken(): ?Token
    {
        return new Token(
            [
                'id' => $this->getBtipayNotify()->token_id,
                'expiration_date' => $this->getBtipayNotify()->token_expiration_date,
            ]
        );
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";

        if ($this->getCodeType() == 0) {
            $content .= "<crc>{$this->getBtipayNotify()->getCrc()}</crc>";
        } else {
            $content .= "<crc error_type=\"{$this->getCodeType()}\" error_code=\"{$this->getCode()}\">";
            $content .= $this->getMessage();
            $content .= "</crc>";
        }

        return $content;
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCodeType()
    {
        return $this->getDataProperty('codeType');
    }

    /**
     * @return false|string
     */
    public function getTransactionDate()
    {
        $timestamp = $this->getBtipayNotify()->timestamp;
        $dateTime = DateTime::createFromFormat('YmdHis', $timestamp);

        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getCardMasked()
    {
        return $this->getBtipayNotify()->pan_masked;
    }
}
