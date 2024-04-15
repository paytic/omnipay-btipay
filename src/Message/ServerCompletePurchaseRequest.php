<?php

namespace Paytic\Omnipay\Btipay\Message;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Paytic\Omnipay\Common\Message\Traits\GatewayNotificationRequestTrait;
use Exception;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 *
 * @method ServerCompletePurchaseResponse send()
 */
class ServerCompletePurchaseRequest extends AbstractRequest
{
    use GatewayNotificationRequestTrait {
        getData as traitGetData;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        try {
            $this->traitGetData();
        } catch (Exception $exception) {
//            $this->setDataItem('code', BtipayAbstractRequest::CONFIRM_ERROR_TYPE_TEMPORARY);
            $this->setDataItem('codeType', $exception->getCode());
            $this->setDataItem('message', $exception->getMessage());
        }

        return $this->getDataArray();
    }

    /**
     * @return mixed
     */
    public function isValidNotification()
    {
        if ($this->httpRequest->getMethod() !=='POST') {
            return false;
        }
        $token = $this->parseToken();
        if (!$token) {
            return false;
        }

        $this->validateToken($token);
        return true;
    }

    protected function parseToken()
    {
        $postContent = $this->httpRequest->getContent();
        if (empty($postContent)) {
            return false;
        }
        $parser = new Parser(new JoseEncoder());

        try {
            $token = $parser->parse($postContent);
        } catch (\Exception $e) {
            return false;
        }

        $this->setDataItem('decodedToken', $token);
        return $token;
    }

    protected function validateToken(Token $token)
    {
        $validator = new Validator();

        try {
            $validator->assert($token, new SignedWith(
                new Sha256(),
                InMemory::base64Encoded('56tLBt/f52zw3WyLjLnl6zNgToJ2AtcSQ7vdQSz+ztM=')
            )); // doesn't throw an exception
//            $validator->assert($token, new IssuedBy('epay-app-02-uat.bt.wan'));
        } catch (RequiredConstraintsViolated $e) {
            // list of constraints violation exceptions:
            throw new Exception('Token validation failed');
        }
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    protected function parseNotification()
    {
        $notification = $this->getDecodedData();

        $this->setDataItem('orderId', $notification['mdOrder']);
        $this->setDataItem('orderNumber', $notification['orderNumber']);
        $this->setDataItem('status', $notification['status']);
        $this->setDataItem('actionCode', $notification['actionCode']);
        $this->setDataItem('actionCodeDescription', $notification['actionCodeDescription']);
        $this->setDataItem('approvalCode', $notification['approvalCode']);

        return $notification;
    }

    /**
     * @return Token
     */
    public function getDecodedToken(): Token
    {
        if (!$this->hasDataItem('decodedToken')) {
           throw new Exception('Token not decoded');
        }
        return $this->getDataItem('decodedToken');
    }

    /**
     * @return bool|mixed|string
     * @throws Exception
     */
    public function getDecodedData()
    {
        if (!$this->hasDataItem('data_decoded')) {
            $data = $this->getDecodedToken()->claims()->get('payload');
            $this->setDataItem('data_decoded', $data);
        }

        return $this->getDataItem('data_decoded');
    }
}
