<?php

namespace Cryptomkt\Wallet\Resource;

class Order extends Resource
{
    // use OrderActiveRecord;

    /** @var string */
    private $amount;

    /** @var string */
    private $market;    

    /** @var string */
    private $price;

    /** @var string */
    private $type;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;


    public function getType()
    {
        return $this->type;
    }

    public function getMarket()
    {
        return $this->market;
    }

    public function setMarket($market)
    {
        $this->market = $market;
    }

    public function getPrice()
    {
        return $this->market;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
