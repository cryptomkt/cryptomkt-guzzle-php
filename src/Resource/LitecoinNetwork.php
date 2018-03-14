<?php

namespace Cryptomkt\Wallet\Resource;

use Cryptomkt\Wallet\Enum\ResourceType;

class LitecoinNetwork extends Resource
{
    public function __construct()
    {
        parent::__construct(ResourceType::LITECOIN_NETWORK);
    }
}