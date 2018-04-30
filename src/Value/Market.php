<?php
    
    namespace Cryptomkt\Exchange\Value;
    
    class Market{
        
        private $name;

        public function __construct($name){
            $this->name = $name;
        }

        public function getName(){
            return $this->name;
        }
    }
?>