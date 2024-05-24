<?php

namespace Paytic\Omnipay\Btipay\Message;

use DateTime;
use Paytic\Omnipay\Common\Message\Traits\HasLanguageRequestTrait;
use Paytic\Omnipay\Common\Message\Traits\RequestDataGetWithValidationTrait;
use Stev\BTIPay\BTIPayClient;
use Stev\BTIPay\Exceptions\ValidationException;
use Stev\BTIPay\Model\BillingInfo;
use Stev\BTIPay\Model\CustomerDetails;
use Stev\BTIPay\Model\DeliveryInfo;
use Stev\BTIPay\Model\Order;
use Stev\BTIPay\Model\OrderBundle;
use Stev\BTIPay\Util\ErrorCodes;

/**
 * Class PurchaseRequest
 * @package Paytic\Omnipay\Btipay\Message
 *
 * @method PurchaseResponse send()
 */
class PurchaseRequest extends AbstractRequest
{
    use RequestDataGetWithValidationTrait;
    use HasLanguageRequestTrait;

    /**
     * @inheritdoc
     */
    public function initialize(array $parameters = [])
    {
        $parameters['currency'] = $parameters['currency'] ?? 'ron';

        return parent::initialize($parameters);
    }


    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    public function validateDataFields(): array
    {
        return [
            'username',
            'password',
            'amount',
            'orderId',
//            'orderId', 'orderName', 'orderDate',
//            'notifyUrl', 'returnUrl', 'signature', 'certificate',
            'card'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function populateData()
    {
        $btClient = $this->getClient();
        $order = $this->generateOrder();
        try {
            $response = $btClient->register($order);
        } catch (ValidationException $exception) {
            print_r(['property' => $exception->getProperty(),
                    'value' => $exception->getValue(),
                    'message' => $exception->getMessage(),
                ]
            );
        }

        $responseErrorCode = $response->getErrorCode();
        if ($responseErrorCode === NULL || $responseErrorCode === ErrorCodes::SUCCESS) {
            //Redirect your user to the received form url
            $data['redirectUrl'] = $response->getFormUrl();
            return $data;
        }

//        var_dump($response);
        echo 'DUPLICATE PAYMENT ATTEMPT';
        die('');
    }

    protected function generateOrder()
    {
        $order = new Order();
        $order->setOrderNumber($this->getOrderId())
            ->setDescription($this->getDescription())
            ->setAmount($this->getAmount() * 100)
            ->setCurrencyAlpha3($this->getCurrency())
            ->setReturnUrl('' . $this->getReturnUrl());
//        $card->confirmUrl = ''.$this->getNotifyUrl(); //Add spaces to add the item to the XML

        $card = $this->getCard();
        $order->setEmail($card->getEmail());

        $order->force3DSecure(true);

        $currentDate = new DateTime();
        $orderBundle = new OrderBundle($currentDate, $this->generateCustomerDetails());

        $order->setOrderBundle($orderBundle);
        return $order;
    }


    /**
     * @return CustomerDetails
     */
    protected function generateCustomerDetails()
    {
        $card = $this->getCard();

        $customerDetails = new CustomerDetails();

        $customerDetails
            ->setContact($card->getBillingName())
            ->setEmail($card->getEmail());
        $phone = $card->getBillingPhone();
        $phone = $phone ?? '0741000000';
        $customerDetails->setPhone($phone);

        $billingInfo = new BillingInfo();
        $deliveryInfo = new DeliveryInfo();
        $deliveryInfo->setDeliveryType('email');

        $this->customerInfoPopulateCountry($billingInfo, $card->getBillingCountry());
        $this->customerInfoPopulateCountry($deliveryInfo, $card->getShippingCountry(), $card->getBillingCountry());

        $this->customerInfoPopulateCity($billingInfo, $card->getBillingCity());
        $this->customerInfoPopulateCity($deliveryInfo, $card->getShippingCity(), $billingInfo->getCity());

        $this->customerInfoPopulateAddress($billingInfo, $card, 'billing');
        $this->customerInfoPopulateAddress($deliveryInfo, $card, 'shipping', $billingInfo->getPostAddress());

        $customerDetails->setBillingInfo($billingInfo);
        $customerDetails->setDeliveryInfo($deliveryInfo);
        return $customerDetails;
    }

    protected function customerInfoPopulateCountry($infoObject, $country, $default = 'RO')
    {
        $country = !empty($country) ? $country : $default;
        if (strlen($country) == 2) {
            $country = strtoupper($country);
        } else {
            $country = strtoupper(substr($country, 0, 2));
        }
        $infoObject->setCountryAlpha2($country);
    }

    protected function customerInfoPopulateCity($infoObject, $city, $default = '----')
    {
        $city = !empty($city) ? $city : $default;
        $infoObject->setCity($city);
    }

    protected function customerInfoPopulateAddress($infoObject, $cardObject, $type = 'billing', $default = 'Str. -----')
    {
        $address = trim(
            $cardObject->{'get' . ucfirst($type) . 'Address1'}()
            . ' '
            . $cardObject->{'get' . ucfirst($type) . 'Address2'}()
        );
        $address = !empty($address) ? $address : $default;
        $infoObject->setPostAddress($address);
    }
}
