<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Dnetix\Redirection\PlacetoPay;
use App\Repositories\OrderRepository;
use App\Http\Requests\Gateways\PlacetoPlay\ShowResultRequest;

class PlacetoPlayController extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Validate the status of payment reference
     *
     * @return \Illuminate\Http\Response
     */
    public function validateStatus($reference)
    {
        $order = $this->orderRepository->getWhere('number',$reference);
        $arrStatus = null;

        $gateway = $order->gateway()->first();
        $dataGateway =  json_decode($gateway->payment_data,true);

        $placetopay = new PlacetoPay([
            'login'     => env('LOGIN_P2P'),
            'tranKey'   => env('TRANKEY_P2P'),
            'url'       => env('BASE_URL_P2P'),
            'rest'      => [
                'timeout' => 45,
                'connect_timeout' => 30,
            ]
        ]);

        $response = $placetopay->query($dataGateway['request_id']);

        if ($response->isSuccessful()) {
            $arrStatus = $response->status()->toArray();
            
            $dataGateway['status'] = $arrStatus['status'];

            $gateway->update([
                'payment_data'  => json_encode($dataGateway)
            ]);
            
            if ($response->status()->isApproved()) {

                $order->fill([
                    'status' => 'PAYED'
                ]);

                $this->orderRepository->save($order);
                
            }

        } else {
            
            return redirect()->back()->with(['errorMessage' => $response->status()->message()]);
            
        }
        return $this->show(new ShowResultRequest([
            'reference' => $reference,
            'status'    => $arrStatus
        ]));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowResultRequest $request)
    {
        $data = $request->all();

        $reference = $data['reference'];
        $status = $data['status']['status'];

        $order = $this->orderRepository->getWhere('number',$reference);
        $gateway = json_decode($order->gateway->payment_data,true);
        
        return view('payments.show',compact('order','status','gateway'));        
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
