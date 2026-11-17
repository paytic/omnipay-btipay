<?php

namespace Paytic\Omnipay\Btipay\Tests;

use Paytic\Omnipay\Btipay\Gateway;
use Paytic\Omnipay\Btipay\Message\PurchaseAndSaveCardRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseRequest;
use Paytic\Omnipay\Btipay\Message\PurchaseWithSavedCardRequest;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class HelperTest
 * @package ByTIC\Omnipay\Twispay\Tests
 */
class GatewayTest extends GatewayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new Gateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }

    public function testPurchaseSelectsTheAppropriateCardOnFileFlow()
    {
        self::assertInstanceOf(PurchaseRequest::class, $this->gateway->purchase());
        self::assertInstanceOf(
            PurchaseAndSaveCardRequest::class,
            $this->gateway->purchase(['clientId' => 'customer-1'])
        );
        self::assertInstanceOf(
            PurchaseWithSavedCardRequest::class,
            $this->gateway->purchase(['clientId' => 'customer-1', 'bindingId' => 'binding-1'])
        );
    }
}
