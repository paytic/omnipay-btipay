<?php

namespace Paytic\Omnipay\Btipay\Message;

class CreateCardRequest extends CardOnFileRegisterRequest
{
    public function getData(): array
    {
        $this->setAmount(0);

        return parent::getData();
    }
}
