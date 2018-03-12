<?php
    namespace Cryptomkt\Exchange\Enum;

    class ErrorCode{
        const AUTHENTICATION_ERROR = 'authentication_error';
        const INTERNAL_SERVER_ERROR = 'internal_server_error';
        const INVALID_REQUEST = 'invalid_request';
        const NOT_FOUND = 'not_found';
        const PARAM_REQUIRED = 'param_required';
        const RATE_LIMIT_EXCEEDED = 'rate_limit_exceeded';

        private function __construct(){
        }
    }
?>