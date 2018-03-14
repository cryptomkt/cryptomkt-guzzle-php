<?php

namespace Cryptomkt\Wallet\Resource;
use Cryptomkt\Wallet\Enum\ResourceType;

class LitecoinAddress extends Resource
{
    private $address;

    public function __construct($address)
    {
        parent::__construct(ResourceType::LITECOIN_ADDRESS);

        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }
}