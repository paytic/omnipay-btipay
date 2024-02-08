<?php

namespace Paytic\Omnipay\Btipay\Message;

use Paytic\Omnipay\Common\Library\Signer;
use Paytic\Omnipay\Common\Message\Traits\GatewayNotificationRequestTrait;
use Paytic\Omnipay\Btipay\Models\Request\AbstractRequest as BtipayAbstractRequest;
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
            $this->setDataItem('code', BtipayAbstractRequest::CONFIRM_ERROR_TYPE_TEMPORARY);
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
        return $this->hasPOST('env_key') && $this->hasPOST('data');
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    protected function parseNotification()
    {
        $xml = $this->getDecodedXML();
        $notification = $this->getBtipayNotify($xml);

        $this->setDataItem('code', $notification->errorCode);
        $this->setDataItem('codeType', $notification->errorCode);
        $this->setDataItem('message', $notification->errorMessage);

        return $notification;
    }

    /**
     * @return string
     */
    public function getDecodedXML()
    {
        $data = $this->getDecodedData();
        $envKey = $this->getDecodedKey();

        $signer = new Signer();
        $signer->setPrivateKey($this->getPrivateKey());

        return $signer->openContentWithRSA($data, $envKey);
    }

    /**
     * @return bool|mixed|string
     * @throws Exception
     */
    protected function getDecodedData()
    {
        if (!$this->hasDataItem('data_decoded')) {
            $data = $this->httpRequest->request->get('data');
            $data = base64_decode($data);
            if ($data === false) {
                throw new Exception(
                    'Failed decoding data',
                    BtipayAbstractRequest::ERROR_CONFIRM_FAILED_DECODING_DATA
                );
            }
            $this->setDataItem('data_decoded', $data);
        }

        return $this->getDataItem('data_decoded');
    }

    /**
     * @return bool|mixed|string
     * @throws Exception
     */
    protected function getDecodedKey()
    {
        if (!$this->hasDataItem('key_decoded')) {
            $envKey = $this->httpRequest->request->get('env_key');
            $envKey = base64_decode($envKey);
            if ($envKey === false) {
                throw new Exception(
                    'Failed decoding envelope key',
                    BtipayAbstractRequest::ERROR_CONFIRM_FAILED_DECODING_ENVELOPE_KEY
                );
            }
            $this->setDataItem('key_decoded', $envKey);
        }

        return $this->getDataItem('key_decoded');
    }

    /**
     * @param $xml
     * @return \Paytic\Omnipay\Btipay\Models\Request\Notify
     */
    public function getBtipayNotify($xml)
    {
        $cardRequest = $this->parseXml($xml);
        $this->setDataItem('cardRequest', $cardRequest);

        return $cardRequest->notifyResponse;
    }

    /**
     * @param $xml
     * @return \Paytic\Omnipay\Btipay\Models\Request\Card
     */
    public function parseXml($xml)
    {
        return BtipayAbstractRequest::factory($xml);
    }
}
