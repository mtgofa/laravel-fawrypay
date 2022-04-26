<?php

namespace MTGofa\FawryPay;

class FawryPay
{
    const FAWRY_URL         = 'https://www.atfawry.com/';
    const FAWRY_URL_TEST    = 'https://atfawry.fawrystaging.com/';

    private $items = array();
    private $customer = array();
    private $enviroment = 'TEST';

    function __construct($enviroment=Null){
        $enviroment = ($enviroment)?$enviroment:config('fawrypay.enviroment');
        $this->merchantCode = ($enviroment=='LIVE')?config('fawrypay.merchant_code'):config('fawrypay.merchant_code_test');
        $this->secureKey = ($enviroment=='LIVE')?config('fawrypay.secure_key'):config('fawrypay.secure_key_test');
        $this->url = ($enviroment=='LIVE')?self::FAWRY_URL:self::FAWRY_URL_TEST;
    }

    public function generateCode($merchantRefNumber,$description=""){
        $fields = array(
            "language" => app()->getLocale()=='ar'?'ar-eg':'en-gb', //ar-eg //en-gb
            "merchantCode" => $this->merchantCode,
            "merchantRefNumber" => $merchantRefNumber,
            "customer" => $this->customer,
            "order" => array(
                'description' => $description,
                'expiry' => config('fawrypay.expiry_in_hours'), //$this->paymentExpiry
                'orderItems' => $this->items,
            ),
        );
        $fields['signature'] = $this->generateSignature($fields);
        return $fields;
    }

    public function checkStatus($merchantRefNumber){
        $signature = $this->merchantCode;
        $signature .= $merchantRefNumber;
        $signature .= $this->secureKey;
        $signature = hash("sha256",$signature);
        $url = $this->url."ECommerceWeb/Fawry/payments/status?merchantCode=".$this->merchantCode."&merchantRefNumber=".$merchantRefNumber."&signature=".$signature;
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($result);
        return $obj;
    }

    public function customer($arr = array()){
        $this->customer = $arr;
    }

    public function addItem($item){
        $this->items[] = $item;
    }

    public function generatePayURL($merchantRefNumber,$description="",$success_url='',$failure_url=''){
        $fawry_url = $this->url.'ECommercePlugin/FawryPay.jsp?chargeRequest=';
        $fawry_url .= json_encode($this->generateCode($merchantRefNumber,$description));
        $fawry_url .= "&successPageUrl=".$success_url;
        $fawry_url .= "&failerPageUrl=".$failure_url;
        return $fawry_url;
    }

    private function generateSignature($fields){
        $signature = $this->merchantCode;
        $signature .= $fields['merchantRefNumber'];
        $signature .= $fields['customer']['customerProfileId']??'';
        foreach($fields['order']['orderItems'] as $item){
            $signature .= $item['productSKU'];
            $signature .= $item['quantity'];
            $signature .= $item['price'];
        }
        $signature .= $fields['order']['expiry'];
        $signature .= $this->secureKey;
        return hash("sha256",$signature);
    }
}
