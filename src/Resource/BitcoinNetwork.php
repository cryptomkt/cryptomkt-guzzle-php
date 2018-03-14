<?php

namespace Cryptomkt\Wallet\Resource;

use Cryptomkt\Wallet\Enum\ResourceType;

class BitcoinNetwork extends Resource
{
    public function __construct()
    {
        parent::__construct(ResourceType::BITCOIN_NETWORK);
    }
}
