<?php

namespace Paytic\Omnipay\Btipay\Utils\Traits;

use Paytic\Omnipay\Btipay\Gateway;

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
        return $this->getParameter(Gateway::SECURITY_PARAM_USERNAME);
    }

    /**
     * @param string|null $value
     * @return self
     */
    public function setUsername(?string $value): self
    {
        return $this->setParameter(Gateway::SECURITY_PARAM_USERNAME, $value);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->getParameter(Gateway::SECURITY_PARAM_PASSWORD);
    }

    /**
     * @param string|null $value
     * @return self
     */
    public function setPassword(?string $value): self
    {
        return $this->setParameter(Gateway::SECURITY_PARAM_PASSWORD, $value);
    }

    /**
     * @return mixed
     */
    public function getCallbackToken()
    {
        return $this->getParameter(Gateway::SECURITY_PARAM_CALLBACK_TOKEN);
    }

    /**
     * @param string|null $value
     * @return self
     */
    public function setCallbackToken(?string $value): self
    {
        return $this->setParameter(Gateway::SECURITY_PARAM_CALLBACK_TOKEN, $value);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getDefaultParameters(): array
    {
        return [
            'testMode' => $this->getTestMode(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'callbackToken' => $this->getCallbackToken(),
//            'card' => [
//                'first_name' => '',
//            ], //Add in order to generate the Card Object
        ];
    }
}
