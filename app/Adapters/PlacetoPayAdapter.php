<?php

namespace App\Adapters;

use App\Product;
use Illuminate\Support\Str;
use Dnetix\Redirection\PlacetoPay;
use App\Contracts\PaymentGatewayInterface as PaymentInterface;

class PlacetoPayAdapter implements PaymentInterface
{
    private $gateway;
    private $orderNumber;
    private $response;

    public function __construct()
    {
        $this->gateway = new PlacetoPay([
            'login'     => env('LOGIN_P2P'),
            'tranKey'   => env('TRANKEY_P2P'),
            'url'       => env('BASE_URL_P2P'),
            'rest'      => [
                'timeout' => 45,
                'connect_timeout' => 30,
            ]
        ]);

        $this->orderNumber = null;
    }

    public function makeRequest(Array $buyerData=null, Product $product)
    {
        $this->setOrderNumber();

        $request = [
            'buyer' => [
                'name'  => $buyerData['name'],
                'email' => $buyerData['email'],
                'mobile'=> $buyerData['mobile'],
            ],
            'payment' => [
                'reference'   => $this->getOrderNumber(),
                'description' => $product->name.", ".$product->description,
                'amount'      => [
                    'currency' => $product->currency,
                    'total' => $product->price,
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => env('APP_URL').'validate-payment/'.$this->getOrderNumber(),
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
        ];            

        $this->response = $this->gateway->request($request);
    }

    public function paymentResult($id)
    {
        $this->response = $this->gateway->query($id);
    }

    public function setOrderNumber()
    {
        $this->orderNumber = str_shuffle(Str::random(5).date('s').mt_rand (100,1000));
    }

    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    public function isSuccessful()
    {
        return $this->response->isSuccessful();
    }

    public function getStatus()
    {
        return $this->response->status();
    }

    public function getProcessUrl()
    {
        return $this->response->processUrl();
    }

    public function getRequestId()
    {
        return $this->response->requestId();
    }
}
