<?php

namespace Paytic\Omnipay\Btipay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class CardOnFileRedirectResponse extends CardOnFileResponse implements RedirectResponseInterface
{
    public function isSuccessful(): bool
    {
        if ($this->getDataProperty('errorCode') !== null) {
            return parent::isSuccessful();
        }

        return !empty($this->getRedirectUrl());
    }

    public function getRedirectUrl()
    {
        return $this->getDataProperty('formUrl', $this->getDataProperty('redirect'));
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return [];
    }
}
