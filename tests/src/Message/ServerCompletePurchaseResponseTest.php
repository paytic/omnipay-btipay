<?php

namespace Paytic\Omnipay\Btipay\Tests\Message;

use Paytic\Omnipay\Btipay\Message\ServerCompletePurchaseRequest;

/**
 * Class ServerCompletePurchaseRequestTest
 * @package Paytic\Omnipay\Mobilpay\Tests\Message
 */
class ServerCompletePurchaseResponseTest extends AbstractResponseTest
{
    public function test_successData()
    {
        $data = [
            'notification' => [
                'mdOrder' => 'a0437b87-ce7f-4402-8b0b-0818352f6c25',
                'orderNumber' => 307924,
                'eci' => '05',
                'actionCode' => '0',
                'approvalCode' => '117595',
                'refNum' => '171617355421',
                'actionCodeDescription' => 'Payment approved and completed successfully',
                'status' => '1',
                'operation' => 'deposited'
            ],
        ];

        $response = $this->newResponse(ServerCompletePurchaseRequest::class, $data);

        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isPending());
        self::assertFalse($response->isCancelled());
        self::assertSame('Payment approved and completed successfully', $response->getMessage());
    }

    public function test_notPaidData()
    {
        $data = [
            'notification' => [
                'mdOrder' => '120b3994-3935-4085-aaa3-86b3ea940616',
                'orderNumber' => 307924,
                'eci' => '07',
                'actionCode' => '-2007',
                'actionCodeDescription' => 'Payment session was expired',
                'status' => '0',
                'operation' => 'declined'
            ],
        ];

        $response = $this->newResponse(ServerCompletePurchaseRequest::class, $data);

        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isPending());
        self::assertFalse($response->isCancelled());
        self::assertSame('Payment session was expired', $response->getMessage());
    }
}
