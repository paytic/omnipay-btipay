<?php

namespace Paytic\Omnipay\Btipay\Message;

class UnbindCardRequest extends BindCardRequest
{
    protected function getAction(): string
    {
        return 'unBindCard';
    }
}
