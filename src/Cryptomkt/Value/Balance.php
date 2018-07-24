<?php
    
    namespace Cryptomkt\Value;
    
    class Balance{

        private $available;
        private $wallet;
        private $balance;

        public function __construct($available, $wallet, $balance){
            $this->available = $available;
            $this->wallet = $wallet;
            $this->balance = $balance;
        }
        
        public function getAvailable(){
            return $this->available;
        }

        public function getWallet(){
            return $this->wallet;
        }

        public function getBalance(){
            return $this->balance;
        }
    }
?>