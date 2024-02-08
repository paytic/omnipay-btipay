<?php

namespace Paytic\Omnipay\Btipay\Gateway;

use Paytic\Omnipay\Btipay\Message\CompletePurchaseRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseRequest;
use Paytic\Omnipay\Btipay\Message\ServerCompletePurchaseRequest;
use Omnipay\Common\Message\RequestInterface;

/**
 * Trait HasRequests
 * @package Paytic\Omnipay\Mobilpay\Gateway
 */
trait HasRequests
{
    /**
     * @inheritdoc
     * @return PurchaseRequest
     */
    public function purchase(array $parameters = []): RequestInterface
    {
        $this->populateRequestLangParam($parameters);

        return $this->createRequest(
            PurchaseRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    /**
     * @inheritdoc
     */
    public function completePurchase(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            CompletePurchaseRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    /**
     * @inheritdoc
     */
    public function serverCompletePurchase(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            ServerCompletePurchaseRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

}
