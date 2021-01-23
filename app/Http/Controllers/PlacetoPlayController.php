<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Dnetix\Redirection\PlacetoPay;

class PlacetoPlayController extends Controller
{
    /**
     * Validate the status of payment reference
     *
     * @return \Illuminate\Http\Response
     */
    public function validateStatus($reference)
    {
        $order = Order::where('number',$reference)->first();

        $gateway = $order->gateway()->first();
        $dataGateway =  json_decode($gateway->payment_data,true);

        $placetopay = new PlacetoPay([
            'login'     => '6dd490faf9cb87a9862245da41170ff2',
            'tranKey'   => '024h1IlD',
            'url'       => 'https://dev.placetopay.com/redirection/',
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
                
                $order->update([
                    'status' => 'PAYED'
                ]);
            }

            if ($response->status()->isRejected()) {
                
                $order->update([
                    'status' => 'REJECTED'
                ]);
            }

        } else {
            
            print_r($response->status()->message() . "\n");
        }

        return redirect()->route('payment-result',['reference' => $reference]);
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
    public function show($reference)
    {
        $order = Order::where('number',$reference)->first();
        return view('payments.show',compact('order'));        
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
