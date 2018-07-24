<?php

    namespace Cryptomkt\Value;

    class Amount{

        private $executed;
        private $original;
        private $remaining;

        public function __construct($executed = null, $original, $remaining = null){
            $this->executed = $executed;
            $this->original = $original;
            $this->remaining = $remaining;
        }

        public function getExecuted(){
            return $this->executed;
        }

        public function getOriginal(){
            return $this->original;
        }

        public function getRemaining(){
            return $this->remaining;
        }

        

    }
?>