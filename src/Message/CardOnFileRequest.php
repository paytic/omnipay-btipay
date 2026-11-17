<?php

namespace Paytic\Omnipay\Btipay\Message;

use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use RuntimeException;
use UnexpectedValueException;

abstract class CardOnFileRequest extends AbstractRequest
{
    abstract protected function getAction(): string;

    abstract public function getData(): array;

    protected function getResponseClass(): string
    {
        return CardOnFileResponse::class;
    }

    public function sendData($data)
    {
        $responseData = $this->sendApiRequest($this->getAction(), $data);
        $responseClass = $this->getResponseClass();

        return new $responseClass($this, $responseData);
    }

    protected function sendApiRequest(string $action, array $data): array
    {
        $username = $this->getUsername();
        $password = $this->getPassword();
        if (empty($username) || empty($password)) {
            throw new RuntimeException('iPay username and password are required.');
        }

        $environment = $this->getTestMode() ? 'ecclients-sandbox.btrl.ro' : 'ecclients.btrl.ro';
        $url = 'https://' . $environment . '/payment/rest/' . $action . '.do';
        $requestFactory = new RequestFactory();
        $streamFactory = new StreamFactory();
        $request = $requestFactory->createRequest('POST', $url)
            ->withHeader('Authorization', 'Basic ' . base64_encode($username . ':' . $password))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request = $request->withBody(
            $streamFactory->createStream(http_build_query($data, '', '&', PHP_QUERY_RFC3986))
        );

        $response = $this->getHttpClient()->sendRequest($request);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new RuntimeException('iPay API returned HTTP ' . $response->getStatusCode() . '.');
        }

        $responseData = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($responseData)) {
            throw new UnexpectedValueException('iPay API returned an invalid response.');
        }

        return $responseData;
    }
}
