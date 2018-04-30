<?php
    
    namespace Cryptomkt\Exchange\Resource;
    
    class Book {

        private $createdAt;
        private $price;
        private $amount;

        public function __construct($createdAt, $price, $amount){
            $this->createdAt = $createdAt;
            $this->price = $price;
            $this->amount = $amount;
        }

        public function getCreateAt(){
            return $this->createAt;
        }

        public function getPrice(){
            return $this->price;
        }

        public function getAmount(){
            return $this->amount;
        }
    }
?>