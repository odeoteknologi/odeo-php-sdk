# odeo-php-sdk

[![Latest Stable Version](https://poser.pugx.org/febrianjiuwira/odeo-php-sdk/v/stable)](https://packagist.org/packages/febrianjiuwira/odeo-php-sdk)
[![Total Downloads](https://poser.pugx.org/febrianjiuwira/odeo-php-sdk/downloads)](https://packagist.org/packages/febrianjiuwira/odeo-php-sdk)
[![Latest Unstable Version](https://poser.pugx.org/febrianjiuwira/odeo-php-sdk/v/unstable)](https://packagist.org/packages/febrianjiuwira/odeo-php-sdk)
[![License](https://poser.pugx.org/febrianjiuwira/odeo-php-sdk/license)](https://packagist.org/packages/febrianjiuwira/odeo-php-sdk)

PHP library for calling https://api.v2.odeo.co.id/ API using GuzzleHttp.

Just install the package to your PHP Project and you're ready to go!

# Dependencies
* [Guzzle](http://docs.guzzlephp.org/en/stable/quickstart.html)

## Development
### Quick Start Guide
1. Install the package with Composer: `composer require febrianjiuwira/odeo-php-sdk` 
2. Use the service you need in your application class - I.e. you are using the Disbursement service (*assuming you're using PSR-4*):
 ```php
use OdeoApi\Services\Disbursement;
```
3. Create a new instance of the class (`Disbursement`) with the your API credentials:
```php
$disbursement = new Disbursement('clientId', 'clientSecret', 'signingKey', 'staging');
```
4. Use one of the class method to query the API - this example will request the bank list:
```php
$disbursement->bankList($token);
```

## Usage
### OdeoApi\OdeoApi
`OdeoApi` class provides the functions that is needed to configure your API calls and signature generation.
```php
$odeo = new OdeoApi('clientId', 'clientSecret', 'signingKey', 'production'); // initialize OdeoApi class

// set API call to odeo production environment
$odeo->production();

// set API call to odeo staging/development environment
$odeo->staging();

// request API access token
$token = json_decode($odeo->requestToken())->access_token;

// request Disbursements Bank List API using createRequest
$odeo->createRequest('GET', '/dg/v1/banks', $token);

// comparing signature from your callbacks
$isValid = $odeo->isValidSignature($signatureToCompare, $method, $path, $token, $timestamp, $body);
```

### OdeoApi\Services\Disbursement
`Disbursement` class extends `OdeoApi` class and simplify clients request for calling Disbursement API services. You'll be able to use some of `OdeoApi` method such as `requestToken` to ease your development.
```php
$disbursement = new Disbursement('clientId', 'clientSecret', 'signingKey', 'production');

// request /dg/v1/bank-account-inquiry API
$disbursement->bankAccountInquiry($token, $accountNo, $bankId, $customerName, $withValidation = false);

// request /dg/v1/banks API
$disbursement->bankList($token);

// request ​/dg​/v1​/disbursements API
$disbursement->executeDisbursement($token, $accountNo, $amount, $bankId, $customerName, $description, $referenceId);

// request /dg/v1/disbursements/reference-id/{reference_id} API
$disbursement->checkDisbursementByReferenceId($token, $referenceId);

// request /dg/v1/disbursements/{disbursement_id} API
$disbursement->checkDisbursementByDisbursementId($token, $disbursementId);

// request /cash/me/balance API
$disbursement->checkBalance($token);
```

### OdeoApi\Services\PaymentGateway
Same as `Disbursement` class, `PaymentGateway` class also extends `OdeoApi` class.
 
```php
$paymentGateway = new PaymentGateway('clientId', 'clientSecret', 'signingKey', 'production');

// request /pg/v1/payment/reference-id/{reference_id} API
$paymentGateway->checkPaymentByReferenceId($token, $referenceId);

// request /pg/v1/payment/{payment_id} API
checkPaymentByPaymentId($token, $paymentId);
```