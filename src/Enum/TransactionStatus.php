<?php

namespace Cryptomkt\Wallet\Enum;

/**
 * Supported transaction statuses.
 */
class TransactionStatus
{
    const CANCELED = 'canceled';
    const COMPLETED = 'completed';
    const CREATED = 'created';

    private function __construct()
    {
    }
}
