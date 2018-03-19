# CryptoMarket PHP SDK

Official SDK library for the [Crypto Market Exchange SDK v1][1] to integrate Coinbase Exchange into your
PHP project.

## Installation

This library could be installed using Composer. Please read the [Composer Documentation](https://getcomposer.org/doc/01-basic-usage.md).

```json
"require": {
    "cryptomkt/cryptomkt": "~1.1"
}
```

## Authentication

### API Key

Use an API key and secret to access your own Crypto Market account.

```php
use Cryptomkt\Exchange\Client;
use Cryptomkt\Exchange\Configuration;

$configuration = Configuration::apiKey($apiKey, $apiSecret);
$client = Client::create($configuration);
```

You can also use the `fetch_all` parameter to have the library issue all the
necessary requests to load the complete collection.

```php
use Cryptomkt\Exchange\Enum\Param;

$transactions = $client->getAccountTransactions($account, [
    Param::FETCH_ALL => true,
]);
```

### Warnings

It's prudent to be conscious of warnings. The library will log all warnings to a
standard PSR-3 logger if one is configured.

```php
use Cryptomkt\Exchange\Client;
use Cryptomkt\Exchange\Configuration;

$configuration = Configuration::apiKey($apiKey, $apiSecret);
$configuration->setLogger($logger);
$client = Client::create($configuration);
```

### Responses

There are multiple ways to access raw response data. First, each resource
object has a `getRawData()` method which you can use to access any field that
are not mapped to the object properties.

```php
$data = $deposit->getRawData();
```

Raw data from the last HTTP response is also available on the client object.

```php
$data = $client->decodeLastResponse();
```

### Active record methods

The library includes support for active record methods on resource objects. You
must enable this functionality when bootstrapping your application.

```php
$client->enableActiveRecord();
```

Once enabled, you can call active record methods on resource objects.

```php
use Cryptomkt\Exchange\Enum\Param;

$transactions = $account->getTransactions([
    Param::FETCH_ALL => true,
]);
```

## Usage

This is not intended to provide complete documentation of the API. For more
detail, please refer to the
[official documentation](https://developers.cryptomkt.com/).

### [Market Data](https://developers.cryptomkt.com/)

**List supported native currencies**

```php
$currencies = $client->getCurrencies();
```

**List exchange rates**

```php
$rates = $client->getExchangeRates();
```

**Buy price**

```php
$buyPrice = $client->getBuyPrice('BTC-USD');
```

**Sell price**

```php
$sellPrice = $client->getSellPrice('BTC-USD');
```

**Spot price**

```php
$spotPrice = $client->getSpotPrice('BTC-USD');
```

**Current server time**

```php
$time = $client->getTime();
```

### [Users](https://developers.coinbase.com/api/v2#users)

**Get authorization info**

```php
$auth = $client->getCurrentAuthorization();
```

**Lookup user info**

```php
$user = $client->getUser($userId);
```

**Get current user**

```php
$user = $client->getCurrentUser();
```

**Update current user**

```php
$user->setName('New Name');
$client->updateCurrentUser($user);
```

### [Accounts](https://developers.coinbase.com/api/v2#accounts)

**List all accounts**

```php
$accounts = $client->getAccounts();
```

**List account details**

```php
$account = $client->getAccount($accountId);
```

**List primary account details**

```php
$account = $client->getPrimaryAccount();
```

**Set account as primary**

```php
$client->setPrimaryAccount($account);
```

**Create a new bitcoin account**

```php
use Cryptomkt\Exchange\Resource\Account;

$account = new Account([
    'name' => 'New Account'
]);
$client->createAccount($account);
```

**Update an account**

```php
$account->setName('New Account Name');
$client->updateAccount($account):
```

**Delete an account**

```php
$client->deleteAccount($account);
```

### [Addresses](https://developers.coinbase.com/api/v2#addresses)

**List receive addresses for account**

```php
$addresses = $client->getAccountAddresses($account);
```

**Get receive address info**

```php
$address = $client->getAccountAddress($account, $addressId);
```

**List transactions for address**

```php
$transactions = $client->getAddressTransactions($address);
```

**Create a new receive address**

```php
use Cryptomkt\Exchange\Resource\Address;

$address = new Address([
    'name' => 'New Address'
]);
$client->createAccountAddress($account, $address);
```

### [Transactions](https://developers.coinbase.com/api/v2#transactions)

**List transactions**

```php
$transactions = $client->getAccountTransactions($account);
```

**Get transaction info**

```php
$transaction = $client->getAccountTransaction($account, $transactionId);
```

**Send funds**

```php
use Cryptomkt\Exchange\Enum\CurrencyCode;
use Cryptomkt\Exchange\Resource\Transaction;
use Cryptomkt\Exchange\Value\Money;

$transaction = Transaction::send([
    'toBitcoinAddress' => 'ADDRESS',
    'amount'           => new Money(5, CurrencyCode::USD),
    'description'      => 'Your first bitcoin!',
    'fee'              => '0.0001' // only required for transactions under BTC0.0001
]);

$client->createAccountTransaction($account, $transaction);
```

**Transfer funds to a new account**

```php
use Cryptomkt\Exchange\Resource\Transaction;
use Cryptomkt\Exchange\Resource\Account;

$fromAccount = Account::reference($accountId);

$toAccount = new Account([
    'name' => 'New Account'
]);
$client->createAccount($toAccount);

$transaction = Transaction::transfer([
    'to'            => $toAccount,
    'bitcoinAmount' => 1,
    'description'   => 'Your first bitcoin!'
]);

$client->createAccountTransaction($fromAccount, $transaction);
```

**Request funds**

```php
use Cryptomkt\Exchange\Enum\CurrencyCode;
use Cryptomkt\Exchange\Resource\Transaction;
use Cryptomkt\Exchange\Value\Money;

$transaction = Transaction::request([
    'amount'      => new Money(8, CurrencyCode::USD),
    'description' => 'Burrito'
]);

$client->createAccountTransaction($transaction);
```

**Resend request**

```php
$account->resendTransaction($transaction);
```

**Cancel request**

```php
$account->cancelTransaction($transaction);
```

**Fulfill request**

```php
$account->completeTransaction($transaction);
```

### [Buys](https://developers.coinbase.com/api/v2#buys)

**List buys**

```php
$buys = $client->getAccountBuys($account);
```

**Get buy info**

```php
$buy = $client->getAccountBuy($account, $buyId);
```

**Buy bitcoins**

```php
use Cryptomkt\Exchange\Resource\Buy;

$buy = new Buy([
    'bitcoinAmount' => 1
]);

$client->createAccountBuy($account, $buy);
```

**Commit a buy**

You only need to do this if you pass `commit=false` when you create the buy.

```php
use Cryptomkt\Exchange\Enum\Param;

$client->createAccountBuy($account, $buy, [Param::COMMIT => false]);
$client->commitBuy($buy);
```

### [Sells](https://developers.coinbase.com/api/v2#sells)

**List sells**

```php
$sells = $client->getAccountSells($account);
```

**Get sell info**

```php
$sell = $client->getAccountSell($account, $sellId);
```

**Sell bitcoins**

```php
use Cryptomkt\Exchange\Resource\Sell;

$sell = new Sell([
    'bitcoinAmount' => 1
]);

$client->createAccountSell($account, $sell);
```

**Commit a sell**

You only need to do this if you pass `commit=false` when you create the sell.

```php
use Cryptomkt\Exchange\Enum\Param;

$client->createAccountSell($account, $sell, [Param::COMMIT => false]);
$client->commitSell($sell);
```

### [Deposit](https://developers.coinbase.com/api/v2#deposits)

**List deposits**

```php
$deposits = $client->getAccountDeposits($account);
```

**Get deposit info**

```php
$deposit = $client->getAccountDeposit($account, $depositId);
```

**Deposit funds**

```php
use Cryptomkt\Exchange\Enum\CurrencyCode;
use Cryptomkt\Exchange\Resource\Deposit;
use Cryptomkt\Exchange\Value\Money;

$deposit = new Deposit([
    'amount' => new Money(10, CurrencyCode::USD)
]);

$client->createAccountDeposit($account, $deposit);
```

**Commit a deposit**

You only need to do this if you pass `commit=false` when you create the deposit.

```php
use Cryptomkt\Exchange\Enum\Param;

$client->createAccountDeposit($account, $deposit, [Param::COMMIT => false]);
$client->commitDeposit($deposit);
```

### [Withdrawals](https://developers.coinbase.com/api/v2#withdrawals)

**List withdrawals**

```php
$withdrawals = $client->getAccountWithdrawals($account);
```

**Get withdrawal**

```php
$withdrawal = $client->getAccountWithdrawal($account, $withdrawalId);
```

**Withdraw funds**

```php
use Cryptomkt\Exchange\Enum\CurrencyCode;
use Cryptomkt\Exchange\Resource\Withdrawal;
use Cryptomkt\Exchange\Value\Money;

$withdrawal = new Withdrawal([
    'amount' => new Money(10, CurrencyCode::USD)
]);

$client->createAccountWithdrawal($account, $withdrawal);
```

**Commit a withdrawal**

You only need to do this if you pass `commit=true` when you call the withdrawal method.

```php
use Cryptomkt\Exchange\Enum\Param;

$client->createAccountWithdrawal($account, $withdrawal, [Param::COMMIT => false]);
$client->commitWithdrawal($withdrawal);
```

### [Payment Methods](https://developers.coinbase.com/api/v2#payment-methods)

**List payment methods**

```php
$paymentMethods = $client->getPaymentMethods();
```

**Get payment method**

```php
$paymentMethod = $client->getPaymentMethod($paymentMethodId);
```

### [Merchants](https://developers.coinbase.com/api/v2#merchants)

#### Get merchant

```php
$merchant = $client->getMerchant($merchantId);
```

### [Orders](https://developers.coinbase.com/api/v2#orders)

#### List orders

```php
$orders = $client->getOrders();
```

#### Get order

```php
$order = $client->getOrder($orderId);
```

#### Create order

```php
use Cryptomkt\Exchange\Resource\Order;
use Cryptomkt\Exchange\Value\Money;

$order = new Order([
    'name' => 'Order #1234',
    'amount' => Money::btc(1)
]);

$client->createOrder($order);
```

#### Refund order

```php
use Cryptomkt\Exchange\Enum\CurrencyCode;

$client->refundOrder($order, CurrencyCode::BTC);
```

### Checkouts

#### List checkouts

```php
$checkouts = $client->getCheckouts();
```

#### Create checkout

```php
use Cryptomkt\Exchange\Resource\Checkout;

$params = array(
    'name'               => 'My Order',
    'amount'             => new Money(100, 'USD'),
    'metadata'           => array( 'order_id' => $custom_order_id )
);

$checkout = new Checkout($params);
$client->createCheckout($checkout);
$code = $checkout->getEmbedCode();
$redirect_url = "https://www.coinbase.com/checkouts/$code";
```

#### Get checkout

```php
$checkout = $client->getCheckout($checkoutId);
```

#### Get checkout's orders

```php
$orders = $client->getCheckoutOrders($checkout);
```

#### Create order for checkout

```php
$order = $client->createNewCheckoutOrder($checkout);
```

### [Notifications webhook and verification](https://developers.coinbase.com/docs/wallet/notifications)

```php
$raw_body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_CB_SIGNATURE'];
$authenticity = $client->verifyCallback($raw_body, $signature); // boolean
```

## Contributing and testing

The test suite is built using PHPUnit. Run the suite of unit tests by running
the `phpunit` command.

```
phpunit
```

There is also a collection of integration tests that issues real requests to the
API and inspects the resulting objects. To run these tests, you must copy
`phpunit.xml.dist` to `phpunit.xml`, provide values for the `CB_API_KEY` and
`CB_API_SECRET` variables, and specify the `integration` group when running the
test suite.

```
phpunit --group integration
```

[1]: https://developers.cryptomkt.com
[2]: https://packagist.org/packages/cryptomkt/cryptomkt