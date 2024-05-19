<?php

namespace Paytic\Omnipay\Btipay\Utils;

use Stev\BTIPay\Util\OrderStatuses;

class OrderStatus
{
    public const STATUSES_SUCCESSFUL = [
        OrderStatuses::STATUS_DEPOSITED_SUCCESSFULLY,
            OrderStatuses::STATUS_PRE_AUTH_HELD
    ];

    public const STATUSES_PENDING =
        [
//            OrderStatuses::STATUS_REGISTERED_BUT_NOT_PAID,
            OrderStatuses::STATUS_AUTH_ACS_INIATED,
        ];

    public const STATUSES_CANCELLED = [
        OrderStatuses::STATUS_AUTH_REVERSED,
        OrderStatuses::STATUS_REFUNDED,
        OrderStatuses::STATUS_AUTH_DECLINED
    ];

    public static function isSuccessful($status): bool
    {
        return in_array($status, self::STATUSES_SUCCESSFUL);
    }

    public static function isPending($status): bool
    {
        return in_array($status, self::STATUSES_PENDING);
    }

    public static function isCancelled($status): bool
    {
        return in_array($status, self::STATUSES_CANCELLED);
    }
}

