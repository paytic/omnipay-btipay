<?php

namespace Paytic\Omnipay\Btipay\Message;

use Paytic\Omnipay\Common\Message\Traits\GatewayNotificationResponseTrait;
use Paytic\Omnipay\Common\Message\Traits\HasTokenTrait;
use Paytic\Omnipay\Common\Models\Token;
use Paytic\Omnipay\Btipay\Models\Request\Card;
use Paytic\Omnipay\Btipay\Models\Request\Notify;
use DateTime;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseResponse extends AbstractResponse
{
    use GatewayNotificationResponseTrait;
    use HasTokenTrait;

    /** @noinspection PhpMissingParentCallCommonInspection
     *
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->getCode() == 0 && in_array($this->getAction(), ['confirmed']);
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        if ($this->hasDataProperty('code')) {
            return $this->getDataProperty('code');
        }

        return parent::getCode();
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->getBtipayNotify()->action;
    }

    /**
     * @return Notify
     */
    public function getBtipayNotify()
    {
        return $this->getDataProperty('notification');
    }

    /**
     * @return Card
     */
    public function getBtipayRequest()
    {
        return $this->getDataProperty('cardRequest');
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isPending()
    {
        if ($this->getCode() == 0) {
            return in_array($this->getAction(), ['paid', 'paid_pending', 'confirmed_pending']);
        }

        return parent::isPending();
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled()
    {
        if ($this->getCode() == 0) {
            return in_array($this->getAction(), ['credit', 'canceled']);
        }

        return parent::isCancelled();
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
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->getDataProperty('message');
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

    /** @noinspection PhpMissingParentCallCommonInspection
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->getBtipayNotify()->purchaseId;
    }

    /**
     * @return string
     */
    public function getCardMasked()
    {
        return $this->getBtipayNotify()->pan_masked;
    }
}
