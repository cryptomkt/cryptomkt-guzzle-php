<?php
    namespace Cryptomkt\Exchange\Exception;

    use Cryptomkt\Exchange\Enum\ErrorCode;
    use Cryptomkt\Exchenge\Value\Error;
    use GuzzleHttp\Exception\RequestException;  
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class HttpException extends RuntimeException{

        private $errors;
        private $request;
        private $response;

        public static function wrap(RequestException $e){
            $response = $e->getResponse();

            if($errors = self::errors($response)){
                $class = self::exceptionClass($response, $errors[0]);
                $message = implode(', ', array_map('strval', $errors));
            }else{
                $class = self::exceptionClass($response);
                $message = $e->getMessage();
            }

            return new $class($message, $errors, $e->getRequest(), $response, $e);
        } 

        public function __construct($message, array $errors, RequestInterface $request, ResponseInterface $response, \Exception $previous){
            parent::__construct($message, 0, $previous);
            $this->errors = $errors;
            $this->request = $request;
            $this->response = $response;
        }

        public function getErrors(){
            return $this->errors;
        }

        public function getError(){
            if (isset($this->errors[0])) {
                return $this->errors[0];
            }
        }

        public function getRequest(){
            return $this->request;
        }

        public function getResponse(){
            return $this->response;
        }
        
        public function getStatusCode(){
            return $this->response->getStatusCode();
        }
        
        /** @return Error[] */
        private static function errors(ResponseInterface $response = null){
            $data = $response ? json_decode($response->getBody(), true) : null;
            if (isset($data['errors'])) {
                // api errors
                $map = function(array $e) { return new Error($e['status'], $e['message']); };
                $errors = array_map($map, $data['errors']);
            } elseif (isset($data['error'])) {
                // oauth error
                $errors = [
                    new Error($data['status'], $data['message']),
                ];
            } else {
                // no errors
                $errors = [];
            }
            return $errors;
        }

        private static function exceptionClass(ResponseInterface $response, Error $error = null){
            if ($error) {
                switch ($error->getId()) {
                    case ErrorCode::PARAM_REQUIRED:
                        return ParamRequiredException::class;
                    case ErrorCode::INVALID_REQUEST:
                        return InvalidRequestException::class;        
                    case ErrorCode::AUTHENTICATION_ERROR:
                        return AuthenticationException::class;                    
                }
            }
            switch ($response->getStatusCode()) {
                case 400:
                    return BadRequestException::class;
                case 401:
                    return UnauthorizedException::class;                 
                case 404:
                    return NotFoundException::class;
                case 429:
                    return RateLimitException::class;
                case 500:
                    return InternalServerException::class;
                case 503:
                    return ServiceUnavailableException::class;
                default:
                    return HttpException::class;
            }
        }
    }
?>