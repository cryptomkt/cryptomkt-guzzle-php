<?php 
	namespace Cryptomkt\Exchange\Authentication;
	
	use Psr\Http\Message\RequestInterface;
	use Psr\Http\Message\ResponseInterface;

	class ApiKeyAuthentication implements Authentication{
		private $apiKey;
		private $apiSecret;

		public function __construct($apiKey, $apiSecret) {
			$this->apiKey = $apiKey;
			$this->apiSecret = $apiSecret;
		}

		public function getApiKey(){
			return $this->apiKey;
		}

		public function setApiKey($apiKey){
			$this->apiKey = $apiKey;
		}

		public function getApiSecret(){
			return $this->apiSecret;
		}

		public function setApiSecret($apiSecret){
        	$this->apiSecret = $apiSecret;
    	}

	    public function getRequestHeaders($path, $body){
	        $timestamp = $this->getTimestamp();
	        $signature = $this->getHash('sha384', $timestamp.$path.$body, $this->apiSecret);
	        return [
				'X-MKT-APIKEY'    => $this->apiKey,
	            'X-MKT-SIGNATURE' => $signature,
	            'X-MKT-TIMESTAMP' => $timestamp,
	        ];
	    }

	    protected function getTimestamp(){
	        return time();
	    }

	    protected function getHash($algo, $data, $key){
	        return hash_hmac($algo, $data, $key);
		}

	}
?>