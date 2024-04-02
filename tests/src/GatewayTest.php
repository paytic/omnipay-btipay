<?php

namespace Paytic\Omnipay\Btipay\Tests;

use Paytic\Omnipay\Btipay\Gateway;
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
}
