# Laravel FawryPay

A Laravel online payment gateway.

## Installation

Require via composer

```bash
composer require mtgofa/laravel-fawrypay
```

run this command to generate the FawryPay configuration file
```bash
php artisan vendor:publish --provider="MTGofa\FawryPay\FawryPayServiceProvider"
```

Any method gets the credentials from `config/fawrypay.php` file, so fill in `merchant_code` and `secure_key` before make any request.

config/fawrypay.php

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FawryPay Enviroment
    |--------------------------------------------------------------------------
    |
    | should be on of this ('TEST','LIVE').
    |
    */

    'enviroment' => 'TEST',

    /*
    |--------------------------------------------------------------------------
    | FawryPay Credentials (Test)
    |--------------------------------------------------------------------------
    |
    | use your fawrypay test credentials
    |
    */
    'merchant_code_test' => '',
    'secure_key_test' => '',

    /*
    |--------------------------------------------------------------------------
    | FawryPay Credentials (LIVE)
    |--------------------------------------------------------------------------
    |
    | use your fawrypay live credentials
    |
    */
    'merchant_code' => '',
    'secure_key' => '',
    
    /*
    |--------------------------------------------------------------------------
    | FawryPay Expiry
    |--------------------------------------------------------------------------
    |
    | use 1 for one hour, 24 for one day, 72 for 3 days and etc
    |
    */
    'expiry_in_hours' => '72',
];

```



## How to use

Generate fawry pay url

```php
use MTGofa\FawryPay\FawryPay;

public function generate(){
    $fawryPay = new FawryPay;

    //optional if you have the customer data
    $fawryPay->customer([
    	'customerProfileId' => '1',
    	'name'              => 'Mohamed Tarek',
    	'email'             => 'example@site.com',
    	'mobile'            => '010******',
    ]);
        
    //you can add this method info foreach if you have multible items
    $fawryPay->addItem([
    	'productSKU'    => '1', //item id
    	'description'   => 'Order 100001 Invoice', //item name
    	'price'         => '50.00', //item price
    	'quantity'      => '1', //item quantity
    ]);

    //generatePayURL('your order unique id','your order discription','success url','failed url')
    $pay_url = $fawryPay->generatePayURL('100001','Order 100001 Invoice','http://success_url','http://failed_url');
    return redirect($pay_url);
}

```


Redirect response / Callback Response / Check status


```php
use MTGofa\FawryPay\FawryPay;

public function callback(){

    $ref_id = NULL;
    if(request('merchantRefNum')){ //fawry failed
        $ref_id = request('merchantRefNum');
    } elseif(request('MerchantRefNo')){ //fawry ipn
        $ref_id = request('MerchantRefNo');
    } elseif(request('chargeResponse')){ //fawry response is success
        $chargeResponse = json_decode(request('chargeResponse'));
        $ref_id = isset($chargeResponse->merchantRefNumber)?$chargeResponse->merchantRefNumber:NULL;
    }

    if(!$ref_id) return abort('404');

    $response = (new FawryPay)->checkStatus($ref_id);
    if (isset($response->paymentStatus) and $response->paymentStatus=='PAID') { //paid
        dd('Paid Successfully',$response);
    } else {
        dd($response);
    }
}

```


Don't forget to excute the callback route from csrf token 


```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/your_callbacl_url_here'
    ];
}


```