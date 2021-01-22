<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Dnetix\Redirection\PlacetoPay;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Product $product)
    {
        return view('orders.create',compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $orderNumber = str_shuffle(Str::random(5).date('s').mt_rand (100,1000));
        
        $product = Product::find($data['product_id']);

        $placetopay = new PlacetoPay([
            'login'     => '6dd490faf9cb87a9862245da41170ff2',
            'tranKey'   => '024h1IlD',
            'url'       => 'https://dev.placetopay.com/redirection/',
            'rest'      => [
                'timeout' => 45,
                'connect_timeout' => 30,
            ]
        ]);

        $request = [
            'buyer' => [
                'name'  => $data['customer_name'],
                'email' => $data['customer_email'],
                'mobile'=> $data['customer_mobile'],
            ],
            'payment' => [
                'reference'   => $orderNumber,
                'description' => $product->name.", ".$product->description,
                'amount'      => [
                    'currency' => $product->currency,
                    'total' => $product->price,
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => env('APP_URL').'payment-result?reference='.$orderNumber,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
        ];
        
        $response = $placetopay->request($request);

        if ($response->isSuccessful()) {
            $newOrder = Order::create([
                'product_id'      => $data['product_id'],
                'number'          => $orderNumber,
                'customer_name'   => $data['customer_name'], 
                'customer_email'  => $data['customer_email'], 
                'customer_mobile' => $data['customer_mobile'],
                'process_url'     => $response->processUrl(),
                'request_id'      => $response->requestId()    
            ]);

            return redirect()->away($newOrder->process_url);

        } else {
            return redirect()->back()->with(['errorProcess'=>$response->status()->message()]);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
