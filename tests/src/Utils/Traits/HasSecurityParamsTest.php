<?php

namespace Paytic\Omnipay\Btipay\Tests\Utils\Traits;

use Paytic\Omnipay\Btipay\Gateway;
use Paytic\Omnipay\Btipay\Tests\AbstractTest;

class HasSecurityParamsTest extends AbstractTest
{
    public function test_get_set_security_key()
    {
        $gateway = new Gateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
        $gateway->initialize([
            'username' => 'username',
            'password' => 'password',
            'callbackToken' => 'callback_token',
        ]);
        self::assertSame('username', $gateway->getUsername());
        self::assertSame('password', $gateway->getPassword());
        self::assertSame('callback_token', $gateway->getCallbackToken());

        $gateway->setUsername('username1');
        self::assertSame('username1', $gateway->getUsername());

        $gateway->setPassword('password1');
        self::assertSame('password1', $gateway->getPassword());

        $gateway->setCallbackToken('callback_token1');
        self::assertSame('callback_token1', $gateway->getCallbackToken());
    }
}
