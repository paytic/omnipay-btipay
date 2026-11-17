<?php

namespace Paytic\Omnipay\Btipay\Tests\Message;

use Omnipay\Common\CreditCard;
use Paytic\Omnipay\Btipay\Message\CardOnFileResponse;
use Paytic\Omnipay\Btipay\Message\CreateCardRequest;
use Paytic\Omnipay\Btipay\Message\GetBindingsRequest;
use Paytic\Omnipay\Btipay\Message\MerchantInitiatedRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseAndSaveCardRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseWithSavedCardRequest;

class CardOnFileRequestsTest extends AbstractRequestTest
{
    public function testCreateCardRegistersZeroAmountWithCustomerId()
    {
        $request = $this->newRequest(CreateCardRequest::class);
        $request->initialize($this->getBaseParameters());

        $data = $request->getData();

        self::assertSame('0', $data['amount']);
        self::assertSame('customer-1', $data['clientId']);
        self::assertSame('946', $data['currency']);
        $orderBundle = json_decode($data['orderBundle'], true);
        self::assertSame(1, preg_match('/^\d{4}-\d{2}-\d{2}$/', $orderBundle['orderCreationDate']));
        self::assertSame('642', $orderBundle['customerDetails']['billingInfo']['country']);
    }

    public function testCustomerInitiatedSavedCardPaymentSetsSCAExemption()
    {
        $parameters = $this->getBaseParameters();
        $parameters['amount'] = 12.34;
        $parameters['bindingId'] = 'binding-1';
        $parameters['scaExemption'] = 'TRA';
        $request = $this->newRequest(PurchaseWithSavedCardRequest::class);
        $request->initialize($parameters);

        $data = $request->getData();

        self::assertSame('1234', $data['amount']);
        self::assertSame('binding-1', $data['bindingId']);
        self::assertSame(
            ['requestedScaExemptionInd' => 'TRA'],
            json_decode($data['jsonParams'], true)
        );
    }

    public function testPurchaseCanSaveCardDuringTheInitialFinancialTransaction()
    {
        $request = $this->newRequest(PurchaseAndSaveCardRequest::class);
        $request->initialize($this->getBaseParameters());

        $data = $request->getData();

        self::assertSame('1000', $data['amount']);
        self::assertSame('customer-1', $data['clientId']);
        self::assertArrayNotHasKey('bindingId', $data);
    }

    public function testGetBindingsIncludesCardArtAndExpiredCardFlags()
    {
        $request = $this->newRequest(GetBindingsRequest::class);
        $request->initialize([
            'clientId' => 'customer-1',
            'includeCardArt' => true,
            'showExpired' => true,
        ]);

        self::assertSame([
            'clientId' => 'customer-1',
            'includeCardArt' => 'true',
            'showExpired' => 'true',
        ], $request->getData());
    }

    public function testMerchantInitiatedRequestUsesMITExemption()
    {
        $parameters = $this->getBaseParameters();
        $parameters['bindingId'] = 'binding-1';
        $request = $this->newRequest(MerchantInitiatedRequest::class);
        $request->initialize($parameters);

        self::assertSame(
            ['requestedScaExemptionInd' => 'MIT'],
            json_decode($request->getData()['jsonParams'], true)
        );
    }

    public function testCardOnFileResponseUsesIpayErrorCode()
    {
        $request = $this->newRequest(CreateCardRequest::class);
        $response = new CardOnFileResponse($request, ['errorCode' => '0', 'bindings' => []]);
        $errorResponse = new CardOnFileResponse($request, ['errorCode' => '5']);

        self::assertTrue($response->isSuccessful());
        self::assertFalse($errorResponse->isSuccessful());
    }

    protected function getBaseParameters(): array
    {
        $card = new CreditCard([
            'firstName' => 'Test',
            'lastName' => 'Customer',
            'email' => 'customer@example.com',
            'billingAddress1' => 'Main Street 1',
            'billingCity' => 'Bucharest',
            'billingCountry' => '642',
        ]);

        return [
            'username' => 'test-user',
            'password' => 'test-password',
            'orderId' => 'order-1',
            'amount' => 10,
            'currency' => 'RON',
            'description' => 'COF test',
            'returnUrl' => 'https://merchant.example/return',
            'clientId' => 'customer-1',
            'card' => $card,
        ];
    }
}
