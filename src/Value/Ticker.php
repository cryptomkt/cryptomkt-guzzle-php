<?php
    
    namespace Cryptomkt\Exchange\Value;
    
    class Ticker{
        
        private $high;
        private $volume;
        private $low;
        private $ask;
        private $timestamp;
        private $bid;
        private $lastPrice;
        private $market;

        public function __construct($high, $volume, $low, $ask, $timestamp, $bid, $lastPrice, $market){
            $this->high = $high;
            $this->low = $low;
            $this->volume = $volume;
            $this->ask = $ask;
            $this->timestamp = $timestamp;
            $this->bid = $bid;
            $this->lastPrice = $lastPrice;
            $this->market = $market;
        }

        public function getHigh(){
            return $this->high;
        }

        public function getLow(){
            return $this->low;
        }

        public function getVolume(){
            return $this->volume;
        }

        public function getAsk(){
            return $this->ask;
        }

        public function getTimestamp(){
            return $this->timestamp;
        }

        public function getBid(){
            return $this->bid;
        }

        public function getLastPrice(){
            return $this->lastPrice;
        }

        public function getMarket(){
            return $this->market;
        }
    }

?>