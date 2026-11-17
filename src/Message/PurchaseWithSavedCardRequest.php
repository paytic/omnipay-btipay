<?php

namespace Paytic\Omnipay\Btipay\Message;

use InvalidArgumentException;

class PurchaseWithSavedCardRequest extends CardOnFileRegisterRequest
{
    public function getData(): array
    {
        if (!$this->getParameter('bindingId')) {
            throw new InvalidArgumentException('The bindingId parameter is required for a saved-card payment.');
        }

        return parent::getData();
    }
}
