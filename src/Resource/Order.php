<?php

    namespace Cryptomkt\Exchange\Resource;

    use Cryptomkt\Exchange\Value;

    class Order extends Resource{

        

        public static function reference($orderId = null){
            return new static(null);
        }

        public function __construct($resourcePath = null){
            parent::__construct(ResourceType::ORDER, $resourcePath);
        }


    }
?>