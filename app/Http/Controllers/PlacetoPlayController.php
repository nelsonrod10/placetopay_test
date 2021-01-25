<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Dnetix\Redirection\PlacetoPay;
use App\Adapters\PlacetoPayAdapter;
use App\Repositories\OrderRepository;
use App\Http\Requests\Gateways\PlacetoPlay\ShowResultRequest;

class PlacetoPlayController extends Controller
{
    private $orderRepository;
    private $placetoPayAdapter;

    public function __construct(OrderRepository $orderRepository, PlacetoPayAdapter $placetoPayAdapter)
    {
        $this->orderRepository = $orderRepository;
        $this->placetoPayAdapter = $placetoPayAdapter;
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

        if(!$order)
        {
            return "tenemos un problema para procesar su solicitud";
        }

        $gateway = $order->gateway()->first();
        $dataGateway =  json_decode($gateway->payment_data,true);

        $this->placetoPayAdapter->paymentResult($dataGateway['request_id']);
        
        if ($this->placetoPayAdapter->isSuccessful()) {
            $arrStatus = $this->placetoPayAdapter->getStatus()->toArray();
            
            $dataGateway['status'] = $arrStatus['status'];

            $gateway->update([
                'payment_data'  => json_encode($dataGateway)
            ]);
            
            if ($this->placetoPayAdapter->getStatus()->isApproved()) {

                $order->fill([
                    'status' => 'PAYED'
                ]);

                $this->orderRepository->save($order);
                
            }

        } else {
            
            return redirect()->back()->with(['errorMessage' => $this->placetoPayAdapter->getStatus()->message()]);
            
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
