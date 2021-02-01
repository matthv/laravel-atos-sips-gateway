# Laravel Atos SIPS Payment

---

This package makes easily integration with the Atos SIPS payment system, which is widely used by the french banks under different names: Mercanet, E-Transactions, Citelis etc.

**Be aware this package only supports the version 2 of Atos SIPS.**

---
[Atos SIPS Official Documentation](https://documentation.sips.worldline.com/fr/WLSIPS.326-UG-Guide-de-demarrage-rapide.html)

---

## Installation

### 1. composer
```
composer require matthv/laravel-atos-sips-gateway
```

### 2. publish 
```
php artisan vendor:publish --provider="Matthv\AtosSipsGateway\Providers\AtosSipsServiceProvider"
```

### 3. configuration

Most of the values come from the Atos dashboard.
You should put the following variables into your .env file :

- ATOS_TEST : `true` to use test environment. Defaults to true.
  provided by Atos dashboard
- ATOS_MERCHANT_ID : merchant id.
- ATOS_SECRET_KEY : secret key.
- ATOS_KEY_VERSION : key version.
- ATOS_INTERFACE_VERSION : interface version.
- ATOS_PRODUCTION_URL : bank production url. Defaults to `https://payment-webinit.mercanet.bnpparibas.net/paymentInit`.
- ATOS_TEST_URL : bank test url. Defaults to `https://payment-webinit-mercanet.test.sips-atos.com/paymentInit`. 
  
You can see all configuration options in `config/atos.php`.
#### Example using Mercanet BNP Paribas :
```
ATOS_TEST=true
ATOS_MERCHANT_ID=211000021310001
ATOS_SECRET_KEY=S9i8qClCnb2CZU3y3Vn0toIOgz3z_aBi79akR30vM9o
ATOS_KEY_VERSION=1
ATOS_INTERFACE_VERSION=HP_2.20
```
Documentation : [First-step](https://documentation.mercanet.bnpparibas.net/index.php?title=Premiers_pas) - [Dashboard-info](https://documentation.mercanet.bnpparibas.net/index.php?title=Obtenir_sa_cl%C3%A9_secr%C3%A8te)

---
## Usage

### 1. Prepare the form payment  
 
To make a basic payment, you will need at least 2 information :
- paymentNumber is used to identify individual transactions. This corresponds to Atos `transactionReference`.
- The amount (integer formatted in cents). example 10.50 € => 1050. The default currency is Euro. 

**This code should be run in a controller. It will return a view which will automatically redirect the customer to the bank website.**


```php 
return app()->make(AtosAuthorization::class)
            ->setPaymentNumber('AABBAA'.rand(1000,9999))
            ->setAmount(1000)
            ->paymentView();
```
You can add Atos SIPS custom fields with the `setCustomParameter` method.

```php
return app()->make(AtosAuthorization::class)
            ->setPaymentNumber('AABBAA'.rand(1000,9999))
            ->setCustomParameters(
                [
                    'customerEmail' => 'j.doe@customer-email.com',
                    'customerId' => 123,
                ]
            )
            ->setAmount(1000)
            ->paymentView();
```

### 2. Return & callback routes

You need to set 2 routes names in `config/atos.php`  :
- `customer_return_route_name` : allows your users to return to your site whenever the payment is successful or cancelled. Defaults to `atos.return`.
- `customer_callback_route_name` : route called back by the bank on transaction completion. Defaults to `atos.callback`.


### 3. Callback transaction handling

This code should be run in controller of the callback route.

```php
$verify = app()->make(Verify::class);
// you can access all callback data using
$allParameters = $verify->getParameters();
// or specify a field using
$paymentNumber = $verify->getParameter('transactionReference');

try {
    $success = $verify->isSuccess();
    if ($success) {
        // handle successful payment
    } else {
        // handle error payment
    }
    echo "OK";
} catch (InvalidSignature $e) {
    Log::alert('Invalid payment signature detected');
}
```

## Licence

This package is licenced under the [MIT license](http://opensource.org/licenses/MIT)

## Thanks

This package is inspired by [devpark/laravel-paybox-gateway](https://github.com/devpark/laravel-paybox-gateway).
