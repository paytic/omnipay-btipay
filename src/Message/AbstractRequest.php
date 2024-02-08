<?php

namespace Paytic\Omnipay\Btipay\Message;

use Paytic\Omnipay\Btipay\Utils\Traits\HasOrderId;
use Paytic\Omnipay\Common\Message\Traits\SendDataRequestTrait;
use Paytic\Omnipay\Btipay\Utils\Traits\HasSecurityParams;
use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;
use Stev\BTIPay\BTIPayClient;

/**
 * Class AbstractRequest
 * @package Paytic\Omnipay\Btipay\Message
 */
abstract class AbstractRequest extends OmnipayAbstractRequest
{
    use SendDataRequestTrait;
    use HasSecurityParams;
    use HasOrderId;

    protected ?BTIPayClient $client = null;

    /**
     * @return null
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->initClient();
        }
        return $this->client;
    }

    /**
     * @param null $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    protected function initClient(): void
    {
        $btClient = new BTIPayClient($this->getUsername(), $this->getPassword(), $this->getTestMode());
        $this->setClient($btClient);
    }

}
