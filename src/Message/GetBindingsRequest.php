<?php

namespace Paytic\Omnipay\Btipay\Message;

use InvalidArgumentException;

class GetBindingsRequest extends CardOnFileRequest
{
    protected function getAction(): string
    {
        return 'getBindings';
    }

    public function getData(): array
    {
        $clientId = $this->getParameter('clientId');
        if ($clientId === null || $clientId === '') {
            throw new InvalidArgumentException('The clientId parameter is required.');
        }

        return [
            'clientId' => $clientId,
            'includeCardArt' => (bool) $this->getParameter('includeCardArt') ? 'true' : 'false',
            'showExpired' => (bool) $this->getParameter('showExpired') ? 'true' : 'false',
        ];
    }
}
