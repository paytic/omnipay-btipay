<?php

namespace Paytic\Omnipay\Btipay\Message;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;
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
        return true;
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
