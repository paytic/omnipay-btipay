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

        self::assertInstanceOf(ServerCompletePurchaseRequest::class, $request);
        self::assertTrue($request->isValidNotification());

        $token = $request->getDecodedToken();
        assertInstanceOf(Token::class, $token);

        self::assertSame(['mdOrder' => '1ec59ecb-07c2-4cc5-9fdb-69987c7d0a2c',
            'orderNumber' => '2021038132635574',
            'eci' => '05',
            'actionCode' => '0',
            'approvalCode' => '224348',
            'refNum' => '002340224348',
            'actionCodeDescription' => 'Payment approved and completed successfully.',
            'status' => '1',
            'operation' => 'approved'],
            $request->getDecodedData());
    }
}
