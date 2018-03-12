<?php
    namespace Cryptomkt\Exchange;

    use Cryptomkt\Exchange\ActiveRecord\ActiveRecordContext;
    use Cryptomkt\Exchange\Enum\Param;

    class Client{

        const VERSION = 'v1';

        private $http;
        private $mapper;

        public static function create(Configuration $configuration){
            return new static(
                $configuration->createHttpClient(),
                $configuration->createMapper()
            );
        }

        public function __construct(HttpClient $http, Mapper $mapper){
            $this->http = $http;
            $this->mapper = $mapper;
        }

        public function getHttpClient(){
            return $this->http;
        }

        public function getMapper(){
            return $this->mapper;
        }

        public function decodeLastResponse(){
            if ($response = $this->http->getLastResponse()) {
                return $this->mapper->decode($response);
            }
        }

        // Public endpoints
        public function getMarkets(){
            return $this->getAndMapData('/v1/market');
        }

        public function getTicker(array $params = []){
            return $this->getAndMap('/v1/ticker',  $params, 'toTicker');
        }

        // Private methods

        private function getAndMapData($path, array $params = []){
            $response = $this->http->get($path, $params);
            return $this->mapper->toData($response);
        }

        private function getAndMap($path, array $params, $mapperMethod, Resource $resource = null){
            $response = $this->http->get($path, $params);
            return $this->mapper->$mapperMethod($response, $resource);
        }

        private function postAndMap($path, array $params, $mapperMethod, Resource $resource = null){
            $response = $this->http->post($path, $params);
            return $this->mapper->$mapperMethod($response, $resource);
        }
    }
?>  