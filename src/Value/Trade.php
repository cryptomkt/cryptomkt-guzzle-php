<?php

    namespace Cryptomkt\Exchange\Value;

    class Trade{
        
        private $marketTaker;
        private $timestamp;
        private $price;
        private $amount;
        private $market;

        public function __construct($marketTaker, $timestamp, $price, $amount, $market){
            $this->marketTrader = $marketTaker;
            $this->timestamp = $timestamp;
            $this->price = $price;
            $this->amount = $amount;
            $this->market = $market;
        }

        public function getMarketTaker(){
            return $this->marketTaker;
        }

        public function getTimestamp(){
            return $this->timestamp;
        }

        public function getPrice(){
            return $this->price;
        }
        
        public function getAmount(){
            return $this->amount;
        }

        public function getMarket(){
            return $this->market;
        }
    }
?>