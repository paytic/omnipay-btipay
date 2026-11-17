<?php

namespace Paytic\Omnipay\Btipay\Message;

use DateTimeImmutable;
use InvalidArgumentException;
use Omnipay\Common\CreditCard;

abstract class CardOnFileRegisterRequest extends CardOnFileRequest
{
    public function initialize(array $parameters = [])
    {
        $parameters['currency'] = $parameters['currency'] ?? 'RON';

        return parent::initialize($parameters);
    }

    protected function getResponseClass(): string
    {
        return CardOnFileRedirectResponse::class;
    }

    public function getData(): array
    {
        $clientId = $this->getParameter('clientId');
        if ($clientId === null || $clientId === '') {
            throw new InvalidArgumentException('The clientId parameter is required for card-on-file payments.');
        }

        $card = $this->getCard();
        if (!$card instanceof CreditCard) {
            throw new InvalidArgumentException('A card containing the customer billing details is required.');
        }
        if (!$this->getOrderId()) {
            throw new InvalidArgumentException('The orderId parameter is required.');
        }
        if (!$this->getReturnUrl()) {
            throw new InvalidArgumentException('The returnUrl parameter is required.');
        }

        $data = [
            'orderNumber' => $this->getOrderId(),
            'amount' => (string) round((float) $this->getAmount() * 100),
            'currency' => $this->getIpayCurrency(),
            'description' => $this->getDescription(),
            'returnUrl' => $this->getReturnUrl(),
            'email' => $card->getEmail(),
            'orderBundle' => json_encode($this->getOrderBundle($card), JSON_THROW_ON_ERROR),
            'clientId' => (string) $clientId,
            'language' => $this->getParameter('language') ?: ($this->getParameter('lang') ?: 'ro'),
        ];

        $bindingId = $this->getParameter('bindingId');
        if ($bindingId !== null && $bindingId !== '') {
            $data['bindingId'] = $bindingId;
        }

        $jsonParams = $this->getJsonParams();
        if ($jsonParams !== null) {
            $data['jsonParams'] = json_encode($jsonParams, JSON_THROW_ON_ERROR);
        }

        return array_filter($data, static function ($value) {
            return $value !== null;
        });
    }

    protected function getAction(): string
    {
        return $this->getParameter('preAuth') ? 'registerPreAuth' : 'register';
    }

    protected function getIpayCurrency(): string
    {
        $currency = strtoupper((string) $this->getCurrency());
        $currencies = ['RON' => '946', 'EUR' => '978', 'USD' => '840'];

        if (isset($currencies[$currency])) {
            return $currencies[$currency];
        }
        if (ctype_digit($currency)) {
            return $currency;
        }

        throw new InvalidArgumentException('Unsupported iPay currency: ' . $currency);
    }

    protected function getJsonParams(): ?array
    {
        $jsonParams = $this->getParameter('jsonParams');
        if (is_string($jsonParams) && $jsonParams !== '') {
            $jsonParams = json_decode($jsonParams, true, 512, JSON_THROW_ON_ERROR);
        }
        if ($jsonParams !== null && !is_array($jsonParams)) {
            throw new InvalidArgumentException('The jsonParams parameter must be an array or JSON object.');
        }

        $exemption = $this->getParameter('scaExemption');
        if ($exemption !== null && $exemption !== '') {
            $exemption = strtoupper($exemption);
            if (!in_array($exemption, ['TRA', 'LVP', 'SCP', 'MIT'], true)) {
                throw new InvalidArgumentException('Unsupported SCA exemption: ' . $exemption);
            }
            $jsonParams = array_merge($jsonParams ?? [], ['requestedScaExemptionInd' => $exemption]);
        }

        return $jsonParams ?: null;
    }

    protected function getOrderBundle(CreditCard $card): array
    {
        $billingAddress = $this->formatAddress($card, 'billing');
        $shippingAddress = $this->formatAddress($card, 'shipping');
        $shippingAddress = array_filter($shippingAddress, static function ($value) {
            return $value !== null && $value !== '';
        });

        return [
            'orderCreationDate' => (new DateTimeImmutable())->format('Y-m-d'),
            'customerDetails' => [
                'email' => $card->getEmail(),
                'phone' => $card->getBillingPhone() ?: '0741000000',
                'contact' => $card->getBillingName(),
                'billingInfo' => $billingAddress,
                'deliveryInfo' => array_merge(['deliveryType' => 'email'], $shippingAddress),
            ],
        ];
    }

    protected function formatAddress(CreditCard $card, string $type): array
    {
        $prefix = $type === 'billing' ? 'Billing' : 'Shipping';
        $country = $card->{'get' . $prefix . 'Country'}();
        $city = $card->{'get' . $prefix . 'City'}();
        $address = trim(
            (string) $card->{'get' . $prefix . 'Address1'}() . ' '
            . (string) $card->{'get' . $prefix . 'Address2'}()
        );

        if ($type === 'shipping') {
            $country = $country ?: $card->getBillingCountry();
            $city = $city ?: $card->getBillingCity();
            $address = $address ?: trim(
                (string) $card->getBillingAddress1() . ' ' . (string) $card->getBillingAddress2()
            );
        }

        $country = $country ?: 'RO';
        if (strlen($country) !== 2 && strlen($country) !== 3) {
            $country = substr($country, 0, 2);
        }

        return [
            'deliveryType' => 'email',
            'country' => strtoupper($country),
            'city' => $city ?: '----',
            'postAddress' => $address ?: 'Str. -----',
            'postalCode' => $type === 'billing' ? $card->getBillingPostcode() : $card->getShippingPostcode(),
        ];
    }
}
