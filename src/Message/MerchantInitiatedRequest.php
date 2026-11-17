<?php

namespace Paytic\Omnipay\Btipay\Message;

use InvalidArgumentException;
use UnexpectedValueException;

class MerchantInitiatedRequest extends CardOnFileRegisterRequest
{
    public function getData(): array
    {
        if (!$this->getParameter('bindingId')) {
            throw new InvalidArgumentException('The bindingId parameter is required for a merchant-initiated payment.');
        }

        $this->setParameter('scaExemption', 'MIT');

        return parent::getData();
    }

    public function sendData($data)
    {
        $registration = $this->sendApiRequest($this->getAction(), $data);
        $orderId = $registration['orderId'] ?? null;
        if (!$orderId) {
            if (isset($registration['errorCode']) && (string) $registration['errorCode'] !== '0') {
                return new CardOnFileResponse($this, $registration);
            }

            throw new UnexpectedValueException(
                $registration['errorMessage'] ?? 'iPay did not return an orderId for the merchant-initiated payment.'
            );
        }

        $responseData = $this->sendApiRequest('paymentOrderBinding', [
            'mdOrder' => $orderId,
            'bindingId' => $data['bindingId'],
        ]);
        $responseData['orderId'] = $orderId;

        return new CardOnFileResponse($this, $responseData);
    }
}
