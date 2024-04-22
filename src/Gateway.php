<?php

namespace Paytic\Omnipay\Btipay;

use Paytic\Omnipay\Common\Gateway\AbstractGateway;
use Paytic\Omnipay\Common\Gateway\Traits\HasLanguageTrait;
use Paytic\Omnipay\Btipay\Utils\Traits\HasSecurityParams;
use Paytic\Omnipay\Btipay\Gateway\HasRequests;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class Gateway
 * @packagePaytic\Omnipay\Btipay
 *
 * @method RequestInterface authorize(array $options = [])
 * @method RequestInterface completeAuthorize(array $options = [])
 * @method RequestInterface capture(array $options = [])
 * @method RequestInterface refund(array $options = [])
 * @method RequestInterface void(array $options = [])
 * @method RequestInterface createCard(array $options = [])
 * @method RequestInterface updateCard(array $options = [])
 * @method RequestInterface deleteCard(array $options = [])
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 */
class Gateway extends AbstractGateway
{
    use HasRequests;
    use HasSecurityParams;
    use HasLanguageTrait;

    public const SECURITY_PARAM_USERNAME = 'username';
    public const SECURITY_PARAM_PASSWORD = 'password';
    public const SECURITY_PARAM_CALLBACK_TOKEN = 'callback_token';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Btipay';
    }
}
