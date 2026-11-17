<?php

namespace Paytic\Omnipay\Btipay\Message;

use InvalidArgumentException;

class PaymentOrderBindingRequest extends CardOnFileRequest
{
    protected function getAction(): string
    {
        return 'paymentOrderBinding';
    }

    public function getData(): array
    {
        $mdOrder = $this->getParameter('mdOrder') ?: $this->getOrderId();
        $bindingId = $this->getParameter('bindingId');
        if (!$mdOrder || !$bindingId) {
            throw new InvalidArgumentException('The mdOrder and bindingId parameters are required.');
        }

        return ['mdOrder' => $mdOrder, 'bindingId' => $bindingId];
    }
}
