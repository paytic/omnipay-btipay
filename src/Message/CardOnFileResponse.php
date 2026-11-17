<?php

namespace Paytic\Omnipay\Btipay\Message;

class CardOnFileResponse extends AbstractResponse
{
    public function isSuccessful(): bool
    {
        $errorCode = $this->getDataProperty('errorCode');
        return $errorCode !== null && (string) $errorCode === '0';
    }

    public function getCode()
    {
        return $this->getDataProperty('errorCode', $this->getDataProperty('actionCode'));
    }

    public function getMessage()
    {
        return $this->getDataProperty(
            'errorMessage',
            $this->getDataProperty('actionCodeDescription', $this->getDataProperty('info'))
        );
    }

    public function getTransactionReference()
    {
        return $this->getDataProperty('orderId');
    }

    public function getBindingId()
    {
        return $this->getDataProperty('bindingInfo.bindingId');
    }

    public function getBindings(): array
    {
        return $this->getDataProperty('bindings', []);
    }
}
