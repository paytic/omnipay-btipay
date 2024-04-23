<?php

namespace Paytic\Omnipay\Btipay\Tests\Message;

use Lcobucci\JWT\Token;
use Paytic\Omnipay\Btipay\Message\ServerCompletePurchaseRequest;
use function PHPUnit\Framework\assertInstanceOf;

class ServerCompletePurchaseRequestTest extends AbstractRequestTest
{
    public function testSimpleSend()
    {
        $request = $this->newRequestFromFileTest(
            ServerCompletePurchaseRequest::class,
            'ServerComplete/demoParams'
        );
        $request->initialize(
            ['callback_token' => getenv('BTIPAY_CALLBACK_TOKEN')]
        );

        self::assertInstanceOf(ServerCompletePurchaseRequest::class, $request);
        self::assertTrue($request->isValidNotification());

        $token = $request->getDecodedToken();
        assertInstanceOf(Token::class, $token);

        self::assertSame(['mdOrder' => '2ccd55a5-27f8-4790-b9ec-9b0d3e0808c9',
            'orderNumber' => '192640',
            'eci' => '05',
            'actionCode' => '0',
            'approvalCode' => '065242',
            'refNum' => '007925172961',
            'actionCodeDescription' => 'Payment approved and completed successfully',
            'status' => '1',
            'operation' => 'deposited'],
            $request->getDecodedData()
        );
    }
}
