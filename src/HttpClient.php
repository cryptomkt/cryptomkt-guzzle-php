<?php
    namespace Cryptomkt\Exchange;

    use Cryptomkt\Exchange\Authentication\Authentication;
    use Cryptomkt\Exchange\Enum\Param;
    use Cryptomkt\Exchange\Exception\HttpException;
    use GuzzleHttp\ClientInterface;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Psr7\Request;
    use GuzzleHttp\RequestOptions;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Log\LoggerInterface;

    class HttpClient{
        private $apiUrl;
        private $apiVersion;
        private $auth;
        private $transport;
        
        private $lastRequest;
        private $lastResponse;

        public function __construct($apiUrl, $apiVersion, Authentication $auth, ClientInterface $transport){
            $this->apiUrl = rtrim($apiUrl, '/');
            $this->apiVersion = $apiVersion;
            $this->auth = $auth;
            $this->transport = $transport;
        }

        // Public methods
        public function getLastRequest(){
            return $this->lastRequest;
        }

        public function getLastResponse(){
            return $this->lastResponse;
        }

        public function get($path, array $params = []){
            return $this->request('GET', $path, $params);
        }

        public function post($path, array $params = []){
            return $this->request('POST', $path, $params);
        }

        // Private methods
        private function request($method, $path, array $params = []){
            if ('GET' === $method) {
                $path = $this->prepareQueryString($path, $params);
            }
            $request = new Request($method, $this->prepareUrl($path));
            return $this->send($request, $params);
        }

        private function send(RequestInterface $request, array $params = []){
            $this->lastRequest = $request;
            $options = $this->prepareOptions(
                $request->getRequestTarget(),
                $params
            );

            try{
                $this->lastResponse = $response = $this->transport->send($request, $options);
            }catch(RequestException $e){
                throw HttpException::wrap($e);
            }

            return $response;
        }

        private function prepareQueryString($path, array &$params = []){
            if(!$params){
                return $path;
            }

            $path .= false === strpos($path, '?') ? '?' : '&';
        }

        private function prepareUrl($path){
            return $this->apiUrl.'/'.ltrim($path, '/');
        }

        private function prepareOptions($path, array $params = []){
            $options = [];

            if($params){
                $options[RequestOptions::JSON] = $data;
                $body = json_encode($data);
            }else{
                $body = '';
            }

            $options[RequestOptions::HEADERS] = $this->auth->getRequestHeaders($path, $body);

            return $options;
        }
    }
?>