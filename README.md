# Laravel FawryPay

A Laravel online payment gateway.

## Installation

Require via composer

```bash
composer require mtgofa/laravel-fawrypay
```

run this command to generate the FawryPay configuration file
```bash
$ php artisan vendor:publish --provider="MTGofa\FawryPay\FawryPayServiceProvider"
```
Then fill in the credentials in `config/fawrypay.php` file. Make sure to make an iframe in your dashboard and get the integration id for payment requests.

Use FawryPay Facade to make requests.


Any method gets the credentials from `config/fawrypay.php` file, so fill in `merchant_code` and `secure_key` before make any request.
