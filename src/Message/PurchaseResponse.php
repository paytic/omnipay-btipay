<?php

namespace Paytic\Omnipay\Btipay\Message;

use Paytic\Omnipay\Common\Message\Traits\RedirectHtmlTrait;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * PayU Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    use RedirectHtmlTrait {
        getRedirectUrl as getRedirectUrlTrait;
    }

    /**
     * @return array
     */
    public function getRedirectData()
    {
        $data = [
        ];

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        $url = $this->getRedirectUrlTrait();
        $lang = $this->getDataProperty('lang', 'ro');
        if ($lang == 'en') {
            $url .= '/'.$lang;
        }

        return $url;
    }
}
