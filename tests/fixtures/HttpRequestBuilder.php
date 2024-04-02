<?php

namespace Paytic\Omnipay\Btipay\Tests\Fixtures;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class HttpRequestBuilder
 * @package Paytic\Omnipay\Euplatesc\Tests\Fixtures
 */
class HttpRequestBuilder
{

    /**
     * @return HttpRequest
     */
    public static function createServerCompletePurchase()
    {
        $request = self::create();
        $request->request->add(self::getFileContents('serverCompletePurchaseParams'));

        return $request;
    }

    /**
     * @return HttpRequest
     */
    public static function create()
    {
        $request = new HttpRequest();

        return $request;
    }

    public static function createFromFile($file)
    {
        $request = self::create();

        $data = self::getFileContents($file);
        $query = $data['get'] ?? [];
        $post = $data['post'] ?? [];
        $attributes = $data['attributes'] ?? [];
        $cookies = $data['cookies'] ?? [];
        $files = $data['files'] ?? [];
        $server = $data['server'] ?? [];
        $content = $data['content'] ?? null;
        $method = $data['method'] ?? 'GET';

        $request->initialize(
            $query,
            $post,
            $attributes,
            $cookies,
            $files,
            $server,
            $content
        );
        $request->setMethod($method);
        return $request;
    }

    /**
     * @param $file
     * @return array
     */
    public static function getFileContents($file)
    {
        return require TEST_FIXTURE_PATH . '/requests/' . $file . '.php';
    }
}
