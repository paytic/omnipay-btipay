<?php

namespace Paytic\Omnipay\Btipay\Message;

use InvalidArgumentException;

class GetOrderStatusExtendedRequest extends CardOnFileRequest
{
    protected function getAction(): string
    {
        return 'getOrderStatusExtended';
    }

    public function getData(): array
    {
        $orderId = $this->getOrderId();
        if (!$orderId) {
            throw new InvalidArgumentException('The orderId parameter is required.');
        }

        return ['orderId' => $orderId];
    }
}
