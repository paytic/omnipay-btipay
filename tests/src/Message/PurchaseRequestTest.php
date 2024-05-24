<?php

namespace Paytic\Omnipay\Btipay\Tests\Message;


use Mockery;
use Omnipay\Common\CreditCard;
use Paytic\Omnipay\Btipay\Message\PurchaseRequest;
use Stev\BTIPay\BTIPayClient;
use Stev\BTIPay\Model\BillingInfo;
use Stev\BTIPay\Model\DeliveryInfo;
use Stev\BTIPay\Model\Order;
use Stev\BTIPay\Responses\RegisterResponse;

class PurchaseRequestTest extends AbstractRequestTest
{
    public function test_create_shippingData()
    {
        $btClient = \Mockery::mock(BTIPayClient::class)->makePartial();
        $btResponse = new RegisterResponse();
        $btResponse->setFormUrl('123');
        $btClient
            ->shouldReceive('register')
            ->with(Mockery::capture($order))
            ->andReturn($btResponse);

        $request = $this->newRequest(PurchaseRequest::class);
        $request->setClient($btClient);

        $dataCard = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'test@yahoo.com',
        ];
        $dataCustomer = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'address1' => 'Test address',
            'city' => 'Test city',
            'state' => 'Test state',
            'postcode' => '123456',
            'country' => 'Romania',
        ];
        foreach (['billing'] as $type) {
            foreach ($dataCustomer as $key => $value) {
                $dataCard[$type . '_' . $key] = $value;
            }
        }
        $card = new CreditCard($dataCard);
        $request->initialize(['username' => '++', 'password' => '', 'amount' => 100, 'orderId' => 123, 'card' => $card]);
        $request->send();

        self::assertInstanceOf(PurchaseRequest::class, $request);
        self::assertInstanceOf(Order::class, $order);

        /** @var Order $order */
        $customerDetails = $order->getOrderBundle()->getCustomerDetails();

        $this->_testCustomerInfo(
            $customerDetails->getBillingInfo(),
            \Stev\BTIPay\Model\BillingInfo::class,
            $dataCustomer
        );

        $this->_testCustomerInfo(
            $customerDetails->getDeliveryInfo(),
            \Stev\BTIPay\Model\DeliveryInfo::class,
            $dataCustomer
        );
    }

    /**
     * @param BillingInfo|DeliveryInfo $actual
     * @param $class
     * @param $data
     * @return void
     */
    protected function _testCustomerInfo($actual, $class, $data)
    {
        self::assertInstanceOf($class, $actual);
        self::assertSame($data['city'], $actual->getCity());
        self::assertSame($data['address1'], $actual->getPostAddress());
    }
}
