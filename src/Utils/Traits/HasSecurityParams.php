<?php

namespace Paytic\Omnipay\Btipay\Utils\Traits;

use Omnipay\Common\AbstractGateway;

/**
 * Trait HasParameters
 * @package Paytic\Omnipay\Mobilpay\Gateway
 */
trait HasSecurityParams
{

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return [
            'testMode' => $this->getTestMode(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'card' => [
                'first_name' => '',
            ], //Add in order to generate the Card Object
        ];
    }
}
