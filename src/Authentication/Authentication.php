<?php 
	namespace Cryptomkt\Exchange\Authentication;

	use Psr\Http\Message\RequestInterface;
	use Psr\Http\Message\ResponseInterface;

	interface Authentication{
			 /**
	     * Returns authentication headers for the given request.
	     *
	     * @param string $path   The request resource path
	     * @param string $body   The request body
	     *
	     * @return array A hash of request headers for authentication
	     */
		 public function getRequestHeaders($path, $body);

	}	
?>