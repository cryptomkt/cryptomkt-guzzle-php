<?php
    namespace Cryptomkt\Exchange;

    use Cryptomkt\Exchange\Enum\ResourceType;
    use Cryptomkt\Exchange\Exception\LogicException;
    use Cryptomkt\Exchange\Exception\RuntimeException;

    use Cryptomkt\Exchange\Resource\Resource;
    use Cryptomkt\Exchange\Resource\ResourceCollection;   
    use Cryptomkt\Exchange\Resource\Order;
    
    use Cryptomkt\Exchange\Values\Balance;
    use Cryptomkt\Exchange\Values\Book;
    use Cryptomkt\Exchange\Values\Market;
    use Cryptomkt\Exchange\Values\Ticker;
    use Cryptomkt\Exchange\Values\Trade;
    
    use Psr\Http\Message\ResponseInterface;
    
    class Mapper{

        private $reflection = [];

        // markets
        public function toMarket(RespondeInterface $response, Market $market = null){
            return $this->toCollection($response, 'injectMarket');
        }

        // tickers
        public function toTicker(ResponseInterfae $response, Ticker $ticker = null){
            return $this->toCollection($response, 'injectTicker');
        }

        // misc
       
        public function toData(ResponseInterface $response){
            return $this->decode($response)['data'];
        }

         /** @return array */
        public function decode(ResponseInterface $response){
            return json_decode($response->getBody(), true);
        }


        // Inject methods
        private function injectMarket(array $data, Market $market = null){
            return $this->injectResource($data, $market ?: new Market());
        }

        private function injectResource(array $data, Resource $resource){
            $properties = $this->getReflectionProperties($resource);

            // add raw data to object
            $properties['raw_data']->setValue($resource, $data);

            foreach ($properties as $key => $property) {
                if (isset($data[$key])) {
                    $property->setValue($resource, $this->toPhp($key, $data[$key]));
                }
            }
        
            return $resource;
        }

        private function getReflectionProperties(Resource $resource){
            $type = $resource->getResourceType();
            if (isset($this->reflection[$type])) {
                return $this->reflection[$type];
            }
            $class = new \ReflectionObject($resource);
            $properties = [];
            do {
                foreach ($class->getProperties() as $property) {
                    $property->setAccessible(true);
                    $properties[self::snakeCase($property->getName())] = $property;
                }
            } while ($class = $class->getParentClass());
            return $this->reflection[$type] = $properties;
        }

        private function toCollection(ResponseInterface $response, $method){
            $data = $this->decode($response);

            $coll = new ResourceCollection(
                $data['pagination']['page'],
                $data['pagination']['previous'],
                $data['pagination']['next']
            );

            foreach ($data['data'] as $resource) {
                $coll->add($this->$method($resource));
            }

            return $coll;
        }
        
        private function toPhp($key, $value){
            if (is_scalar($value)) {
                // misc
                return $value;
            }

            if (is_integer(key($value))) {
                // list
                $list = [];
                foreach ($value as $k => $v) {
                    $list[$k] = $this->toPhp($k, $v);
                }
                return $list;
            }

            if (isset($value['resource'])) {
                // resource
                return $this->createResource($value['resource'], $value);
            }

            if (isset($value['amount']) && isset($value['currency'])) {
                // money
                return new Money($value['amount'], $value['currency']);
            }

            if ('network' === $key && isset($value['status'])) {
                // network
                return new Network($value['status'], isset($value['hash']) ? $value['hash'] : null);
            }

            if (isset($value['type']) && isset($value['amount']) && isset($value['amount']['amount']) && isset($value['amount']['currency'])) {
                // fee
                return new Fee($value['type'], new Money($value['amount']['amount'], $value['amount']['currency']));
            }
        }

        private function fromPhp($value){

            if (is_scalar($value)) {
                // misc
                return $value;
            }
            if (is_array($value)) {
                // list
                $list = [];
                foreach ($value as $k => $v) {
                    $list[$k] = $this->fromPhp($v);
                }
                return $list;
            }

            if ($value instanceof \DateTime) {
                // timestamp
                return $value->format(\DateTime::ISO8601);
            }
            if ($value instanceof Email) {
                // email
                return [
                    'resource' => ResourceType::EMAIL,
                    'email' => $value->getEmail(),
                ];
            }
            if ($value instanceof BitcoinAddress) {
                // bitcoin address
                return [
                    'resource' => ResourceType::BITCOIN_ADDRESS,
                    'address' => $value->getAddress(),
                ];
            }
            
            if ($value instanceof Resource) {
                // resource
                return [
                    'id' => $value->getId(),
                    'resource' => $value->getResourceType(),
                    'resource_path' => $value->getResourcePath(),
                ];
            }

            if ($value instanceof Money) {
                // money
                return [
                    'amount' => $value->getAmount(),
                    'currency' => $value->getCurrency(),
                ];
            }

            if ($value instanceof Network) {
                // network
                $data = ['status' => $value->getStatus()];
                if ($hash = $value->getHash()) {
                    $data['hash'] = $hash;
                }
                return $data;
            }
            if ($value instanceof Fee) {
                // fee
                return [
                    'type' => $value->getType(),
                    'amount' => [
                        'amount' => $value->getAmount()->getAmount(),
                        'currency' => $value->getAmount()->getCurrency(),
                    ],
                ];
            }
            // fail quietly
            return $value;
        }

        private static function snakeCase($word){
            // copied from doctrine/inflector
            return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $word));
        }

        private function createResource($type, array $data){
            $expanded = $this->isExpanded($data);
            switch ($type) {
                case ResourceType::ACCOUNT:
                    return $expanded ? $this->injectAccount($data) : new Account($data['resource_path']);
                case ResourceType::ADDRESS:
                    return $expanded ? $this->injectAddress($data) : new Address($data['resource_path']);
                case ResourceType::APPLICATION:
                    return $expanded ? $this->injectApplication($data) : new Application($data['resource_path']);
                case ResourceType::BITCOIN_ADDRESS:
                    return new BitcoinAddress($data['address']);
                case ResourceType::BUY:
                    return $expanded ? $this->injectBuy($data) : new Buy($data['resource_path']);
                case ResourceType::CHECKOUT:
                    return $expanded ? $this->injectCheckout($data) : new Checkout($data['resource_path']);
                case ResourceType::DEPOSIT:
                    return $expanded ? $this->injectDeposit($data) : new Deposit($data['resource_path']);
                case ResourceType::EMAIL:
                    return new Email($data['email']);
                case ResourceType::MERCHANT:
                    return $expanded ? $this->injectMerchant($data) : new Merchant($data['resource_path']);
                case ResourceType::ORDER:
                    return $expanded ? $this->injectOrder($data) : new Order($data['resource_path']);
                case ResourceType::PAYMENT_METHOD:
                    return $expanded ? $this->injectPaymentMethod($data) : new PaymentMethod($data['resource_path']);
                case ResourceType::SELL:
                    return $expanded ? $this->injectSell($data) : new Sell($data['resource_path']);
                case ResourceType::TRANSACTION:
                    return $expanded ? $this->injectTransaction($data) : new Transaction(null, $data['resource_path']);
                case ResourceType::USER:
                    return $expanded ? $this->injectUser($data) : new User($data['resource_path']);
                case ResourceType::WITHDRAWAL:
                    return $expanded ? $this->injectWithdrawal($data) : new Withdrawal($data['resource_path']);
                case ResourceType::NOTIFICATION:
                    return $expanded ? $this->injectNotification($data) : new Notification($data['resource_path']);
                case ResourceType::BITCOIN_NETWORK:
                    return new BitcoinNetwork();
                default:
                    throw new RuntimeException('Unrecognized resource type: '.$type);
            }
        }

        private function isExpanded(array $data){
            return (Boolean) array_diff(array_keys($data), ['id', 'resource', 'resource_path']);
        }
    }
?>