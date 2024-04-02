<?php

namespace Paytic\Omnipay\Btipay\Tests\Message;

use Paytic\Omnipay\Btipay\Message\AbstractRequest;
use Paytic\Omnipay\Btipay\Tests\AbstractTest;
use Paytic\Omnipay\Btipay\Tests\Fixtures\HttpRequestBuilder;

/**
 * Class AbstractRequestTest
 * @package Paytic\Omnipay\Euplatesc\Tests\Message
 */
abstract class AbstractRequestTest extends AbstractTest
{
    protected function newRequestFromFileTest($class, $file)
    {
        $httpRequest = HttpRequestBuilder::createFromFile($file);
        $request = $this->newRequest($class, null, $httpRequest);
        self::assertInstanceOf($class, $request);
        return $request;
    }

    /**
     * @param string $class
     * @param array $data
     * @return AbstractRequest
     */
    protected function newRequestWithInitTest($class, $data)
    {
        $request = $this->newRequest($class);
        self::assertInstanceOf($class, $request);
        $request->initialize($data);
        return $request;
    }

    /**
     * @param string $class
     * @return AbstractRequest
     */
    protected function newRequest($class, $httpClient = null, $httpRequest = null)
    {
        $client = $httpClient?? $this->getHttpClient();
        $request = $httpRequest ?? $this->getHttpRequest();
        return new $class($client, $request);
    }
}
