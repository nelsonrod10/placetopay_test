<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use App\PaymentGateway;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Dnetix\Redirection\PlacetoPay;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::all();

        return view('orders.index',compact('orders'));
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
    public function store(StoreOrderRequest $request)
    {
        $data = $request->all();

        $orderNumber = str_shuffle(Str::random(5).date('s').mt_rand (100,1000));
        
        $product = Product::find($data['product_id']);

        $placetopay = new PlacetoPay([
            'login'     => env('LOGIN_P2P'),
            'tranKey'   => env('TRANKEY_P2P'),
            'url'       => env('BASE_URL_P2P'),
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
            'returnUrl' => env('APP_URL').'validate-payment/'.$orderNumber,
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
            ]);

            PaymentGateway::create([
                'order_id'      => $newOrder->id,
                'enterprise'    => 'PlaceToPay',
                'payment_data'  => json_encode([
                    'process_url' => $response->processUrl(),
                    'request_id'  => $response->requestId(),
                    'status'      => 'PENDING'      
                ])
            ]);
            return redirect()->route('orders.show',$newOrder);        

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
    public function show(Order $order)
    {
        $gateway = $order->gateway()->first();
        $payment = json_decode($gateway->payment_data,true);

        return view('orders.show',compact('order','payment'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        return view('orders.edit',compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->all();
        
        $placetopay = new PlacetoPay([
            'login'     => env('LOGIN_P2P'),
            'tranKey'   => env('TRANKEY_P2P'),
            'url'       => env('BASE_URL_P2P'),
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
                'reference'   => $order->number,
                'description' => $order->product->name.", ".$order->product->description,
                'amount'      => [
                    'currency' => $order->product->currency,
                    'total' => $order->product->price,
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => env('APP_URL').'validate-payment/'.$order->number,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
        ];
        
        $response = $placetopay->request($request);

        if ($response->isSuccessful()) {
            $order->update([
                'customer_name'   => $data['customer_name'], 
                'customer_email'  => $data['customer_email'], 
                'customer_mobile' => $data['customer_mobile'],
            ]);
                
            $order->gateway->update([
                'payment_data'  => json_encode([
                    'process_url' => $response->processUrl(),
                    'request_id'  => $response->requestId(),
                    'status'      => 'PENDING'      
                ])
            ]);
            return redirect()->route('orders.show',$order);        

        } else {
            return redirect()->back()->with(['errorProcess'=>$response->status()->message()]);
        }
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
