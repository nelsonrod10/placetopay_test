<?php

namespace App\Http\Controllers;


use App\Order;
use App\Product;
use App\PaymentGateway;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Adapters\PlacetoPayAdapter;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PaymentGatewayRepository;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;

class OrdersController extends Controller
{
    private $orderRepository;
    private $productRepository;
    private $paymentRepository;
    private $placetoPayAdapter;

    public function __construct(OrderRepository $orderRepository, 
            ProductRepository $productRepository, 
            PaymentGatewayRepository $paymentGatewayRepository,
            PlacetoPayAdapter $placetoPayAdapter
        )
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->paymentRepository = $paymentGatewayRepository;
        $this->placetoPayAdapter = $placetoPayAdapter;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderRepository->all();

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

    private function makePaymentRequest($data, Product $product)
    {
        $buyerData = [
            'name'   => $data['customer_name'], 
            'email'  => $data['customer_email'], 
            'mobile' => $data['customer_mobile'],
        ];

        $this->placetoPayAdapter->makeRequest($buyerData,$product);
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
        
        $product =  $this->productRepository->get($data['product_id']);

        $this->makePaymentRequest($data,$product);
        
        if ($this->placetoPayAdapter->isSuccessful()) {

            $newOrder = $this->orderRepository->save(
                            new Order([
                                'product_id'      => $data['product_id'],
                                'number'          => $this->placetoPayAdapter->getOrderNumber(),
                                'customer_name'   => $data['customer_name'], 
                                'customer_email'  => $data['customer_email'], 
                                'customer_mobile' => $data['customer_mobile'],
                        ]));

            $this->paymentRepository->save(
                new PaymentGateway([
                    'order_id'      => $newOrder->id,
                    'enterprise'    => 'PlaceToPay',
                    'payment_data'  => json_encode([
                        'process_url' => $this->placetoPayAdapter->getProcessUrl(),
                        'request_id'  => $this->placetoPayAdapter->getRequestId(),
                        'status'      => 'PENDING'      
                    ])
                ])
            );                        
            
            return redirect()->route('orders.show',$newOrder);        

        } else {
            return redirect()->back()->with(['errorProcess'=>$this->placetoPayAdapter->getStatus()->message()]);
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

        $this->makePaymentRequest($data,$order->product);

        if ($this->placetoPayAdapter->isSuccessful()) {
            $order->fill([
                'customer_name'   => $data['customer_name'], 
                'customer_email'  => $data['customer_email'], 
                'customer_mobile' => $data['customer_mobile'],
            ]);

            $updateOrder = $this->orderRepository->save($order);
                
            $paymentGateway = $updateOrder->gateway->fill([
                'payment_data'  => json_encode([
                    'process_url' => $this->placetoPayAdapter->getProcessUrl(),
                    'request_id'  => $this->placetoPayAdapter->getRequestId(),
                    'status'      => 'PENDING'      
                ])
            ]);

            $this->paymentRepository->save($paymentGateway);
            
            return redirect()->route('orders.show',$updateOrder);        

        } else {
            return redirect()->back()->with(['errorProcess'=>$this->placetoPayAdapter->getStatus()->message()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $this->orderRepository->delete($order);

        return redirect('/');
    }
}
