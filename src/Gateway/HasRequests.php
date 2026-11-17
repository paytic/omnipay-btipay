<?php

namespace Paytic\Omnipay\Btipay\Gateway;

use Paytic\Omnipay\Btipay\Message\CompletePurchaseRequest;
use Paytic\Omnipay\Btipay\Message\BindCardRequest;
use Paytic\Omnipay\Btipay\Message\CreateCardRequest;
use Paytic\Omnipay\Btipay\Message\GetBindingsRequest;
use Paytic\Omnipay\Btipay\Message\GetOrderStatusExtendedRequest;
use Paytic\Omnipay\Btipay\Message\MerchantInitiatedRequest;
use Paytic\Omnipay\Btipay\Message\PaymentOrderBindingRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseAndSaveCardRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseWithSavedCardRequest;
use Paytic\Omnipay\Btipay\Message\ServerCompletePurchaseRequest;
use Paytic\Omnipay\Btipay\Message\UnbindCardRequest;
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
        $requestClass = PurchaseRequest::class;
        if (!empty($parameters['bindingId'])) {
            $requestClass = PurchaseWithSavedCardRequest::class;
        } elseif (isset($parameters['clientId']) && $parameters['clientId'] !== '') {
            $requestClass = PurchaseAndSaveCardRequest::class;
        }

        return $this->createRequest(
            $requestClass,
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

    public function createCard(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            CreateCardRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function purchaseWithSavedCard(array $parameters = []): RequestInterface
    {
        $this->populateRequestLangParam($parameters);

        return $this->createRequest(
            PurchaseWithSavedCardRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function merchantInitiatedPurchase(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            MerchantInitiatedRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function paymentOrderBinding(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            PaymentOrderBindingRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function getBindings(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            GetBindingsRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function bindCard(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            BindCardRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function unbindCard(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            UnbindCardRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function deleteCard(array $parameters = []): RequestInterface
    {
        return $this->unbindCard($parameters);
    }

    public function updateCard(array $parameters = []): RequestInterface
    {
        return $this->bindCard($parameters);
    }

    public function getOrderStatusExtended(array $parameters = []): RequestInterface
    {
        return $this->createRequest(
            GetOrderStatusExtendedRequest::class,
            array_merge($this->getDefaultParameters(), $parameters)
        );
    }

    public function fetchTransaction(array $parameters = []): RequestInterface
    {
        return $this->getOrderStatusExtended($parameters);
    }

}
