<?php

namespace Paytic\Omnipay\Btipay\Message;

use InvalidArgumentException;

class BindCardRequest extends CardOnFileRequest
{
    protected function getAction(): string
    {
        return 'bindCard';
    }

    public function getData(): array
    {
        return $this->getBindingData();
    }

    protected function getBindingData(): array
    {
        $bindingId = $this->getParameter('bindingId');
        if (!$bindingId) {
            throw new InvalidArgumentException('The bindingId parameter is required.');
        }

        return ['bindingId' => $bindingId];
    }
}
