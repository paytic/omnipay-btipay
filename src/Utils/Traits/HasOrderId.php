<?php

namespace Paytic\Omnipay\Btipay\Utils\Traits;

/**
 * Trait HasOrderId
 * @package Paytic\Omnipay\Mobilpay\Utils\Traits
 */
trait HasOrderId
{

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }
}
